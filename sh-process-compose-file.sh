#!/bin/sh
#
# Render a compose file for docker stack deploy (stdout).
#
# Usage: ./sh-process-compose-file.sh <compose-file> > <processed-compose-file>
#
# `docker stack deploy` validates against the legacy Compose v3 schema, which is
# narrower than the Compose Specification that `docker compose config` emits, so
# the rendered output needs these fixups:
#
# - drops the compose "name:" header
# - removes quotes around published port values
# - removes quotes around numeric config/secret file modes

set -e

usage() {
  printf 'Usage: %s <compose-file>\n' "$0" >&2
  exit 1
}

[ "$#" -eq 1 ] || usage
[ -f "$1" ] || {
  printf '%s: file not found: %s\n' "$0" "$1" >&2
  exit 1
}

compose_file="$1"

docker compose -f "$compose_file" config \
  | sed -e '/^name:/d' \
        -e '/published:/ s/"//g' \
        -e 's/^\([[:space:]-]*mode:[[:space:]]*\)"\([0-9][0-9]*\)"[[:space:]]*$/\1\2/'
