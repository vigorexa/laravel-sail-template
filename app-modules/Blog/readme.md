# Blog Module

> Пример интеграции модуля с Filament-панелью и межмодульной связью.

## Модели

- **`ArticleModel`** (`blog_articles`) — статья с полем `text` и связью `author` → `UserModel` из модуля AccessControl.

## Filament

Ресурс «Статьи» доступен в группе навигации «Блог» через `BlogFilamentPlugin`.

## Web

Публичная лента последних статей: `GET /blog` (`blog.index`).
