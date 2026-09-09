<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\UserModels\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Password;
use Modules\AccessControl\Filament\Resources\PermissionModels\Schemas\PermissionModelForm;
use Modules\AccessControl\Filament\Resources\RoleModels\Schemas\RoleModelForm;
use Modules\AccessControl\Models\PermissionModel;
use Modules\AccessControl\Models\Role\RoleModel;

class UserModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make()->tabs([
                    Tabs\Tab::make('Основное')->schema([
                        TextInput::make('id')
                            ->label('ID')
                            ->disabled()
                            ->visible(fn (string $operation) => $operation === 'edit')
                            ->copyable(),
                        TextInput::make('name')
                            ->label('Имя')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Section::make()
                            ->label('Пароль')
                            ->columns()
                            ->visible(fn (string $operation) => $operation === 'create')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Пароль')
                                    ->password()
                                    ->revealable()
                                    ->rules(['required', 'confirmed', Password::default()]),
                                TextInput::make('password_confirmation')
                                    ->label('Подтверждение пароля')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->dehydrated(false),
                            ]),
                    ]),

                    Tabs\Tab::make('Права доступа')->schema([
                        Select::make('roles')
                            ->label('Роли')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionAction(fn () => Action::make('createRole')->authorize('access-control.role.create'))
                            ->createOptionForm(fn (Schema $schema) => RoleModelForm::configure($schema))
                            ->live()
                            ->partiallyRenderComponentsAfterStateUpdated(['all_permissions']),

                        Select::make('permissions')
                            ->label('Прямые права')
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionAction(fn () => Action::make('createPermission')->authorize('access-control.permission.create'))
                            ->createOptionForm(fn (Schema $schema) => PermissionModelForm::configure($schema))
                            ->live()
                            ->partiallyRenderComponentsAfterStateUpdated(['all_permissions']),

                        TextEntry::make('all_permissions')
                            ->label('Все права')
                            ->state(fn (Get $get) => Collection::make()
                                ->merge(
                                    RoleModel::query()->whereIn('id', $get('roles'))->get()
                                        ->map(fn (RoleModel $role) => $role->permissions)
                                        ->flatten(),
                                )
                                ->merge(
                                    PermissionModel::query()->whereIn('id', $get('permissions'))->get(),
                                )
                                ->unique('id'))
                            ->badge(),
                    ]),
                ]),
            ]);
    }
}
