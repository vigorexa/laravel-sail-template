<?php

declare(strict_types=1);

namespace Modules\Blog\Filament\Resources\ArticleModels\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Blog\Filament\Resources\ArticleModels\ArticleModelResource;

class ListArticleModels extends ListRecords
{
    protected static string $resource = ArticleModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
