<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\PermissionModels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PermissionModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('label')
                    ->label('Название'),
                TextInput::make('name')
                    ->label('Системное имя')
                    ->required(),
                TextInput::make('guard_name')
                    ->label('Guard')
                    ->default('web')
                    ->disabled(),
                Textarea::make('description')
                    ->label('Описание'),
                Select::make('roles')
                    ->label('Роли')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }
}
