<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\RoleModels;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\AccessControl\Filament\Resources\RoleModels\Pages\ListRoleModels;
use Modules\AccessControl\Filament\Resources\RoleModels\Schemas\RoleModelForm;
use Modules\AccessControl\Filament\Resources\RoleModels\Tables\RoleModelsTable;
use Modules\AccessControl\Models\Role\RoleModel;
use UnitEnum;

class RoleModelResource extends Resource
{
    protected static ?string $model = RoleModel::class;

    protected static ?string $modelLabel = 'роль';

    protected static ?string $pluralModelLabel = 'Роли';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|UnitEnum|null $navigationGroup = 'Управление доступом';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $navigationLabel = 'Роли';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return RoleModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoleModelsTable::configure($table);
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
            'index' => ListRoleModels::route('/'),
        ];
    }
}
