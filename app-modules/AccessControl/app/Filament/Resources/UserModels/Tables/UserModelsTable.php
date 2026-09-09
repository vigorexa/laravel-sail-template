<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\UserModels\Tables;

use App\Filament\Components\TableColumns\IdColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;
use Modules\AccessControl\Models\UserModel;

class UserModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Роли')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('Редактировать'),
                Action::make('password_reset')
                    ->hiddenLabel()
                    ->icon(Heroicon::Key)
                    ->tooltip('Сбросить пароль')
                    ->schema([
                        TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->revealable()
                            ->rules(['required', 'confirmed', Password::default()]),
                        TextInput::make('password_confirmation')
                            ->label('Подтверждение пароля')
                            ->required()
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ])
                    ->action(function (UserModel $record, array $data): void {
                        $record->update(['password' => $data['password']]);
                    }),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip('Удалить'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
