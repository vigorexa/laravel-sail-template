<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\UserModels;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\AccessControl\Filament\Resources\UserModels\Pages\ListUserModels;
use Modules\AccessControl\Filament\Resources\UserModels\Schemas\UserModelForm;
use Modules\AccessControl\Filament\Resources\UserModels\Tables\UserModelsTable;
use Modules\AccessControl\Models\UserModel;
use UnitEnum;

class UserModelResource extends Resource
{
    protected static ?string $model = UserModel::class;

    protected static ?string $modelLabel = 'пользователь';

    protected static ?string $pluralModelLabel = 'Пользователи';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|UnitEnum|null $navigationGroup = 'Управление доступом';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    protected static ?string $navigationLabel = 'Пользователи';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return UserModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserModelsTable::configure($table);
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
            'index' => ListUserModels::route('/'),
        ];
    }


}
