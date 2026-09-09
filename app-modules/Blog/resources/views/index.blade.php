<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Блог</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">

        <style>
            body {
                margin: 0;
                font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
                background: #f9fafb;
                color: #111827;
                line-height: 1.5;
            }

            .container {
                max-width: 48rem;
                margin: 0 auto;
                padding: 2.5rem 1.5rem;
            }

            h1 {
                font-size: 1.875rem;
                font-weight: 600;
                margin: 0 0 2rem;
            }

            .articles {
                display: grid;
                gap: 1.5rem;
            }

            article {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 0.75rem;
                padding: 1.5rem;
            }

            article h2 {
                font-size: 1.25rem;
                font-weight: 600;
                margin: 0 0 0.5rem;
            }

            .meta {
                font-size: 0.875rem;
                color: #6b7280;
                margin-bottom: 1rem;
            }

            .excerpt {
                color: #374151;
            }

            .empty {
                color: #6b7280;
            }

            .pagination {
                margin-top: 2rem;
                display: flex;
                justify-content: center;
            }
        </style>
    </head>
    <body>
        <main class="container">
            <h1>Блог</h1>

            @if ($articles->isEmpty())
                <p class="empty">Статей пока нет.</p>
            @else
                <div class="articles">
                    @foreach ($articles as $article)
                        <article>
                            <h2>{{ $article->title }}</h2>
                            <p class="meta">
                                {{ $article->author?->name ?? '—' }}
                                ·
                                {{ $article->created_at?->format('d.m.Y H:i') }}
                            </p>
                            <div class="excerpt">
                                {!! str(strip_tags($article->text))->limit(300) !!}
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination">
                    {{ $articles->links() }}
                </div>
            @endif
        </main>
    </body>
</html>
