<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <style>
            :root {
                color-scheme: light dark;
            }

            body {
                margin: 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
                font-size: 16px;
                line-height: 1.5;
                color: #1f2328;
                background: #ffffff;
            }

            @media (prefers-color-scheme: dark) {
                body {
                    color: #e6edf3;
                    background: #0d1117;
                }
            }

            .markdown-body {
                box-sizing: border-box;
                max-width: 980px;
                margin: 0 auto;
                padding: 32px 24px 48px;
            }

            .markdown-body h1,
            .markdown-body h2,
            .markdown-body h3,
            .markdown-body h4 {
                margin-top: 1.5em;
                margin-bottom: 0.75em;
                font-weight: 600;
                line-height: 1.25;
            }

            .markdown-body h1 {
                padding-bottom: 0.3em;
                font-size: 2em;
                border-bottom: 1px solid #d0d7de;
            }

            .markdown-body h2 {
                padding-bottom: 0.3em;
                font-size: 1.5em;
                border-bottom: 1px solid #d0d7de;
            }

            .markdown-body h3 {
                font-size: 1.25em;
            }

            .markdown-body p,
            .markdown-body ul,
            .markdown-body ol,
            .markdown-body table,
            .markdown-body pre {
                margin-top: 0;
                margin-bottom: 16px;
            }

            .markdown-body ul,
            .markdown-body ol {
                padding-left: 2em;
            }

            .markdown-body li + li {
                margin-top: 0.25em;
            }

            .markdown-body a {
                color: #0969da;
                text-decoration: none;
            }

            .markdown-body a:hover {
                text-decoration: underline;
            }

            .markdown-body code {
                padding: 0.2em 0.4em;
                font-size: 85%;
                font-family: ui-monospace, SFMono-Regular, SF Mono, Menlo, Consolas, Liberation Mono, monospace;
                background: rgba(175, 184, 193, 0.2);
                border-radius: 6px;
            }

            .markdown-body pre {
                padding: 16px;
                overflow: auto;
                font-size: 85%;
                line-height: 1.45;
                background: #f6f8fa;
                border-radius: 6px;
            }

            .markdown-body pre code {
                padding: 0;
                background: transparent;
                border-radius: 0;
            }

            .markdown-body table {
                width: 100%;
                overflow: auto;
                border-collapse: collapse;
                display: block;
            }

            .markdown-body th,
            .markdown-body td {
                padding: 6px 13px;
                border: 1px solid #d0d7de;
            }

            .markdown-body th {
                font-weight: 600;
                background: #f6f8fa;
            }

            .markdown-body blockquote {
                margin: 0 0 16px;
                padding: 0 1em;
                color: #656d76;
                border-left: 0.25em solid #d0d7de;
            }

            @media (prefers-color-scheme: dark) {
                .markdown-body h1,
                .markdown-body h2 {
                    border-bottom-color: #30363d;
                }

                .markdown-body a {
                    color: #4493f8;
                }

                .markdown-body pre,
                .markdown-body th {
                    background: #161b22;
                }

                .markdown-body th,
                .markdown-body td {
                    border-color: #30363d;
                }

                .markdown-body blockquote {
                    color: #9198a1;
                    border-left-color: #30363d;
                }
            }
        </style>
    </head>
    <body>
        <article class="markdown-body">
            {!! $content !!}
        </article>
    </body>
</html>
