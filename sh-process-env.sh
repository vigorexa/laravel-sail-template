#!/usr/bin/env bash
#
# Merges dotenv files and prints the result to stdout as a flat, interpolated
# list of assignments. Comments and blank lines are dropped.
#
# The first file is the template: only the keys it declares are printed, unless
# --fill-missing is given. ${VAR} references are expanded against the merged
# result and the environment; write $${VAR} to emit a literal ${VAR}.
#
# Usage: ./sh-process-env.sh [--fill-missing] <.env_file> [<.env_file> ...]

set -euo pipefail

# Parallel arrays keep the merged result (bash 3.2 compatible, no associative
# arrays).
env_keys=()
env_values=()

# Keys declared by the first file, which decides what gets printed.
template_keys=()

fill_missing=0

readonly MAX_INTERPOLATION_PASSES=64

# $$ is parked on this byte between loading and printing, so that an escaped
# reference cannot be expanded even after it travels through another variable.
readonly ESCAPED_DOLLAR=$'\001'

die() {
  printf 'process-env: %s\n' "$1" >&2
  exit 1
}

usage() {
  printf '%s\n' \
    "Usage: $0 [--fill-missing] <.env_file> [<.env_file> ...]" \
    '' \
    'Options:' \
    '  -f, --fill-missing  also print keys the first file does not declare' \
    '  -h, --help          show this help' >&2
  exit 1
}

trim() {
  local value="$1"
  value="${value#"${value%%[![:space:]]*}"}"
  value="${value%"${value##*[![:space:]]}"}"
  printf '%s' "$value"
}

index_of_key() {
  local key="$1"
  local i

  for ((i = 0; i < ${#env_keys[@]}; i++)); do
    if [ "${env_keys[$i]}" = "$key" ]; then
      printf '%s' "$i"
      return 0
    fi
  done

  printf '%s' '-1'
}

put_var() {
  local key="$1"
  local value="$2"
  local index

  index="$(index_of_key "$key")"

  if [ "$index" -ge 0 ]; then
    env_values[$index]="$value"
  else
    env_keys+=("$key")
    env_values+=("$value")
  fi
}

get_var() {
  local key="$1"
  local index

  index="$(index_of_key "$key")"

  if [ "$index" -ge 0 ]; then
    printf '%s' "${env_values[$index]}"
    return 0
  fi

  return 1
}

is_template_key() {
  local key="$1"
  local i

  for ((i = 0; i < ${#template_keys[@]}; i++)); do
    if [ "${template_keys[$i]}" = "$key" ]; then
      return 0
    fi
  done

  return 1
}

# Unwraps a quoted value. Double quotes keep backslash escapes for \n and \t,
# single quotes are taken literally, as dotenv implementations do.
unwrap_value() {
  local value="$1"

  if [ ${#value} -ge 2 ] && [ "${value:0:1}" = '"' ] && [ "${value: -1}" = '"' ]; then
    value="${value:1:${#value}-2}"
    value="${value//\\n/$'\n'}"
    value="${value//\\t/$'\t'}"
    value="${value//\\\"/\"}"
    printf '%s' "$value"
    return 0
  fi

  if [ ${#value} -ge 2 ] && [ "${value:0:1}" = "'" ] && [ "${value: -1}" = "'" ]; then
    printf '%s' "${value:1:${#value}-2}"
    return 0
  fi

  # Unquoted values lose everything after an inline comment.
  value="${value%%[[:space:]]#*}"
  trim "$value"
}

load_dotenv() {
  local file="$1"
  local line key value

  [ -f "$file" ] || die "file not found: $file"
  [ -r "$file" ] || die "file not readable: $file"

  while IFS= read -r line || [ -n "$line" ]; do
    line="${line%$'\r'}"
    line="$(trim "$line")"

    [ -n "$line" ] || continue
    [ "${line:0:1}" != '#' ] || continue

    if [[ "$line" =~ ^export[[:space:]]+(.*)$ ]]; then
      line="$(trim "${BASH_REMATCH[1]}")"
    fi

    [[ "$line" =~ ^([A-Za-z_][A-Za-z0-9_]*)=(.*)$ ]] || continue

    key="${BASH_REMATCH[1]}"
    value="$(unwrap_value "$(trim "${BASH_REMATCH[2]}")")"

    put_var "$key" "${value//\$\$/$ESCAPED_DOLLAR}"
  done < "$file"
}

# Expands ${VAR} references against the merged map, falling back to the
# environment. Unknown references collapse to an empty string.
interpolate() {
  local output="$1"
  local pattern='\$\{([A-Za-z_][A-Za-z0-9_]*)\}'
  local pass previous var_name var_value

  for ((pass = 0; pass < MAX_INTERPOLATION_PASSES; pass++)); do
    [[ "$output" =~ $pattern ]] || break

    previous="$output"
    var_name="${BASH_REMATCH[1]}"
    var_value="$(get_var "$var_name" || printf '%s' "${!var_name-}")"

    output="${output//\$\{$var_name\}/$var_value}"

    # A self-reference would otherwise spin until the pass limit.
    [ "$output" != "$previous" ] || break
  done

  printf '%s' "$output"
}

quote_value() {
  local value="$1"

  # A literal $ or ` must survive re-parsing, so single quotes are the only
  # safe wrapper.
  if [[ "$value" == *'$'* || "$value" == *'`'* || "$value" == *'\'* ]]; then
    printf "'%s'" "${value//\'/\'\\\'\'}"
    return 0
  fi

  if [ -z "$value" ] || [[ "$value" =~ [[:space:]\"\'\#\;\&\|\<\>\(\)\*\?] ]]; then
    printf '"%s"' "${value//\"/\\\"}"
    return 0
  fi

  printf '%s' "$value"
}

main() {
  local template file key value index

  while [ "$#" -gt 0 ]; do
    case "$1" in
      -f | --fill-missing)
        fill_missing=1
        shift
        ;;
      -h | --help)
        usage
        ;;
      --)
        shift
        break
        ;;
      -*)
        die "unknown option: $1"
        ;;
      *)
        break
        ;;
    esac
  done

  [ "$#" -ge 1 ] || usage

  template="$1"
  shift

  load_dotenv "$template"

  [ "${#env_keys[@]}" -eq 0 ] || template_keys=("${env_keys[@]}")

  for file in "$@"; do
    load_dotenv "$file"
  done

  for ((index = 0; index < ${#env_keys[@]}; index++)); do
    key="${env_keys[$index]}"

    if [ "$fill_missing" -eq 0 ] && ! is_template_key "$key"; then
      continue
    fi

    value="$(interpolate "${env_values[$index]}")"

    printf '%s=%s\n' "$key" "$(quote_value "${value//$ESCAPED_DOLLAR/\$}")"
  done
}

main "$@"
