<?php

declare(strict_types=1);

namespace Modules\Blog\Filament\Resources\ArticleModels;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Blog\Filament\Resources\ArticleModels\Pages\ListArticleModels;
use Modules\Blog\Filament\Resources\ArticleModels\Schemas\ArticleModelForm;
use Modules\Blog\Filament\Resources\ArticleModels\Tables\ArticleModelsTable;
use Modules\Blog\Models\ArticleModel;
use UnitEnum;

class ArticleModelResource extends Resource
{
    protected static ?string $model = ArticleModel::class;

    protected static ?string $modelLabel = 'статья';

    protected static ?string $pluralModelLabel = 'Статьи';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|UnitEnum|null $navigationGroup = 'Блог';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = 'Статьи';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ArticleModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArticleModelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticleModels::route('/'),
        ];
    }
}
