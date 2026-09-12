# Laravel 12 Sail Template
Мой шаблон для разработки [Twelve-Factor](https://12factor.net/ru/) приложений Laravel v12

## Основные изменения
- Сборка основана на Laravel Sail — [документация](https://laravel.com/docs/12.x/sail). Базовый образ приложения: [`vigorexa/laravel-sail-core:php85-alpine-slim-latest`](https://hub.docker.com/r/vigorexa/laravel-sail-core)
- Предустановлен фреймворк для создания админ панелей Filament [https://filamentphp.com/docs](https://filamentphp.com/docs/4.x/panels/installation)
- Предустановлена система модулей [`nwidart/laravel-modules`](https://laravelmodules.com/docs/13) с кастомной конфигурацией и stub файлами. (Подробнее в разделе ##Модули)
- Предустановлены базовые пакеты:
    - [`laravel/pint`](https://laravel.com/docs/12.x/pint). Конфигурация: https://raw.githubusercontent.com/vigorexa/pint-config/refs/heads/main/pint.json
    - [`laravel/octane`](https://laravel.com/docs/12.x/octane). Сборка готова к использованию с сервером Swoole
    - [`laravel/boost`](https://laravel.com/docs/12.x/boost). Написаны кастомные скиллы. (Подробнее в разделе ##Laravel Boost)
    - А также [`laravel/horizon`](https://laravel.com/docs/12.x/horizon), [`laravel/telescope`](https://laravel.com/docs/12.x/telescope)
- Созданы docker-compose файлы инфраструктуры и приложения для деплоя проекта на удаленный сервер. (Подробнее в разделе ##Деплой)
- Добавлен `project.gitlab-ci.yml` с lint > build > test > deploy стадиями для develop и prod окружений.
- Модуль AccessControl с базовым управлением Пользователями, Ролями и Правами. Основа: [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v7)

---

## Подготовка шаблона
1. Клонировать git репозиторий этого шаблона. И удалить папку `.git`, так как она содержит в себе git конфиги и историю шаблона
2. Заменить плейсхолдеры в **composer.json**: Атрибуты `name` и `description`
3. Скопировать с заменой `project.readme.md` > `readme.md`. И заменить плейсхолдеры `[ProjectName]` и `[ProjectDescription]`.
4. Скопировать `project.gitlab-ci.yml` > `.gitlab-ci.yml` и заполнить переменные из шапки. При необходимости, раскомментировать deploy стадию
5. Следовать пунктам "Развертывание проекта"

---

## Развертывание проекта
1. Скопировать `.env.example` в `.env`. И `.env.testing.example` в `.env.testing`
2. Запустить установку пакетов командой `make sail-init`.
  - Прим. для Windows: Скопируй соответствующую команду из `Makefile`
3. Запустить сборку проекта: `./vendor/bin/sail up -d --build`
4. Сгенерировать уникальный ключ криптографии: `./vendor/bin/sail artisan key:generate`
5. Создать S3 bucket в RustFS: `make rustfs-create-bucket`
6. Накатить миграции в базу данных: `./vendor/bin/sail artisan migrate`
7. Просеять базу тестовыми данными: `./vendor/bin/sail artisan db:seed && ./vendor/bin/sail artisan module:seed --all`
8. Создать бакет laravel в rustfs: `make rustfs-create-bucket`
8. Для работы с AI Агентами установить Laravel Boost: `./vendor/bin/sail artisan boost:install`
  - Прим. для PhpStorm AI Assistant: _см. раздел Laravel Boost этого Readme_

---

## Laravel Boost
Предустановлен пакет [`laravel/boost`](https://laravel.com/docs/12.x/boost) - MCP Сервер с командами для Laravel

Стоит держать сгенерированный конфигурационный файл MCP (`.mcp.json`), файлы рекомендаций (`CLAUDE.md`, `AGENTS.md`, `junie/` и т. д.) 
и конфигурационный файл `boost.json` в файле `.gitignore`, поскольку они автоматически перегенерируются при запуске `boost:install` и `boost:update`.

Добавлены кастомные скиллы для пакета [`nwidart/laravel-modules`](https://laravelmodules.com/docs/13). Они не были предоставлены разработчиками 

### Настройка PhpStorm AI Assistant
Плагин по-умолчанию использует глобальный Workdir, поэтому подключить MCP нужно вручную:
- Добавить сервер: _Settings > Tools > AI Assistant > MCP > "+"_:
  - **Config**: `"laravel-boost": {"command": "./vendor/bin/sail", "args": ["artisan", "boost:mcp"]}`;
  - **WorkDir**: _абсолютный путь к проекту на компьютере_;
  - **ServerLevel**: `Project`;

---

## Модули

Шаблон использует систему модулей [`nwidart/laravel-modules`](https://laravelmodules.com/docs/13) с кастомной конфигурацией

Все модули размещаются в директории app-modules/

Создание модуля стандартно:
```bash
./vendor/bin/sail php artisan module:make
```

Автозагрузка классов модуля подключается через `wikimedia/composer-merge-plugin` — в корневом `composer.json` уже настроено слияние `app-modules/*/composer.json`.
Основной сервис-провайдер объявлен в `module.json`. Не требует ручного подключения.

### Filament

Filament-плагин модуля указывается в `module.json` → `filament.plugin`. При `module:make` stub автоматически
создаёт класс плагина и запись в манифесте; `AdminPanelProvider` подхватывает все включённые модули через
`ModuleFilamentPlugins::discover()`.

```json
{
  "filament": {
    "plugin": "Modules\\AccessControl\\Filament\\AccessControlFilamentPlugin"
  }
}
```

Для существующего модуля без плагина:

```bash
./vendor/bin/sail artisan module:make-filament-plugin AccessControl
```

---

## Полезности

### Sail Terminal Alias
Для того чтобы упростить написание sail команд, можно добавить алиас в свой терминал:
```
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Строка добавляется в зависимости от используемого терминала по-умолчанию
- **zsh** - `~/.zshrc`
- **bash** - `~/.bashrc`
- **PowerShell** - _TODO_

После перезапуска терминала команды будет писать проще.
```shell
sail tinker # === ./vendor/bin/sail tinker
```

### Настройка XDebug

#### Переменные окружения
- **SAIL_XDEBUG_MODE** - переключение режимов XDebug (включить: `develop,debug`, выключить: `off`)
- **SAIL_XDEBUG_CONFIG** - конфигурация XDebug
  - **client_host** - хост для отправки запросов XDebug 
    - MacOS, Windows: `host.docker.internal`
    - Linux: Результат выполнения команды `docker inspect -f {{range.NetworkSettings.Networks}}{{.Gateway}}{{end}} <container-name>`
  - **idekey** - Ключ для фильтрации запросов

Пример:
```dotenv
SAIL_XDEBUG_MODE=develop,debug
SAIL_XDEBUG_CONFIG="client_host=host.docker.internal idekey=Docker"
```

#### Настройка сервера для приема XDebug для **PhpStorm**:
- Добавить сервер: _Settings > PHP > Servers > "+"_
  - **Name**: `{Любой:-Docker}`
  - **Host**: `{хост laravel :-localhost}`
  - **Port**: `{внешний порт laravel :-80}`
  - **Debugger**: `XDebug`
  - **Use path mappings**: `true`
  - Указать mapping для файлов проекта: `/var/www/html`
- Добавить переменную окружения **PHP_IDE_CONFIG** `"serverName={имя_созданного_сервера:-Docker}"`
- Создать дебаг конфигурацию: _Run/Debug Configurations > "+" > PHP Remote Debug_
  - **Name**: `{Любой :-Docker}` 
  - **Filter debug connections...**: `true`
  - **Server**: `{Созданный ранее сервер :-Docker}`
  - **IDE Key**: `{Любой :-Docker}`

#### Настройка сервера для приема XDebug для **VSCode**:
- _TODO_

---

## Деплой

В шаблоне, в директории `./deployment`, находится все необходимое для развертывания приложения на удаленном окружении.

### Стеки

| Стек             | Назначение                             | Контейнеры                                       | После деплоя                                   |
|------------------|----------------------------------------|--------------------------------------------------|------------------------------------------------|
| `app_monitoring` | Метрики, логи и дашборды               | Prometheus, Grafana, Loki, OTEL, Alloy, cAdvisor | `make -C deployment grafana-dashboards-import` |
| `app_traefik`    | Reverse proxy, маршрутизация и SSL     | Traefik                                          | —                                              |
| `app_pgsql`      | База данных PostgreSQL                 | PostgreSQL, pgAdmin, postgres_exporter           | —                                              |
| `app_keydb`      | Кэш, сессии и очереди (Redis)          | KeyDB, redis_exporter                            | —                                              |
| `app_mailpit`    | Перехват исходящей почты (dev/staging) | Mailpit                                          | —                                              |
| `app_rustfs`     | Файловое хранилище S3                  | RustFS (S3 + Console)                            | `make -C deployment rustfs-create-bucket`      |
| `app_laravel`    | Само приложение Laravel                | `laravel` (Octane), `horizon`, `cron`            | Миграции в контейнере `laravel`, сидер         |


### Переменные окружения

Все переменные окружения хранятся в одном родительском `./deployment/.env`

Он создается путем копирования `./deployment/.env.example` и заполнением недостающих значений.
Для удобства они отмечены комментарием `#CHANGEME`

Перед деплоем стека необходимо исполнить команду, которая заполнит `.env.example` сервиса значениями из основного
```bash
./deployment/sh-process-env.sh ./deployment/$(SERVICE)/.env.example .env > ./deployment/$(SERVICE)/.env
```


### Makefile (`deployment/Makefile`)

| Команда                                                  | Описание                                    |
|----------------------------------------------------------|---------------------------------------------|
| `make -C deployment stack-service-deploy SERVICE=<name>` | Деплой одного стека (см. таблицу выше)      |
| `make -C deployment stack-full-deploy`                   | Деплой всех стеков                          |
| `make -C deployment stack-full-rm`                       | Удаление всех стеков                        |
| `make -C deployment rustfs-create-bucket`                | Создание S3-bucket `laravel` в стеке RustFS |
| `make -C deployment grafana-dashboards-import`           | Импорт дашбордов в стек monitoring          |


### Вспомогательные скрипты

- **`deployment/sh-process-env.sh [--fill-missing] <.env_file> [<.env_file> ...]`** — заполнение шаблона `.env.example` с интерполяцией `${VAR}`. Флаг `--fill-missing` переключает скрипт в режим слияния
- **`deployment/sh-process-compose-file.sh <compose-file>`** — подготовка compose-файла для `docker stack deploy`.


### Минимальный воркфлоу ручного деплоя

1. Клон исходников проекта на сервер - `git clone`
2. Запуск сборки соответствующего образа - `docker build -f ./docker/laravel/Dockerfile --target production -t laravel-application:production .`
3. Копирование и заполнение переменных окружения проекта - `cp ./deployment/.env.example ./deployment/.env`
4. Деплой всех стеков проекта - `make -C deployment stack-full-deploy`
5. Создание бакета s3 для Laravel - `make -C deployment rustfs-create-bucket`
6. Импорт Grafana дашбордов - `make -C deployment grafana-dashboards-import`
