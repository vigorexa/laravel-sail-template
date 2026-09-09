<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\PermissionModels;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\AccessControl\Filament\Resources\PermissionModels\Pages\ListPermissionModels;
use Modules\AccessControl\Filament\Resources\PermissionModels\Schemas\PermissionModelForm;
use Modules\AccessControl\Filament\Resources\PermissionModels\Tables\PermissionModelsTable;
use Modules\AccessControl\Models\PermissionModel;
use UnitEnum;

class PermissionModelResource extends Resource
{
    protected static ?string $model = PermissionModel::class;

    protected static ?string $modelLabel = 'право доступа';

    protected static ?string $pluralModelLabel = 'Права доступа';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|UnitEnum|null $navigationGroup = 'Управление доступом';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;

    protected static ?string $navigationLabel = 'Права доступа';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PermissionModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionModelsTable::configure($table);
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
            'index' => ListPermissionModels::route('/'),
        ];
    }
}
