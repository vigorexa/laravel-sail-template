<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\RoleModels\Tables;

use App\Filament\Components\TableColumns\IdColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoleModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Системное имя')
                    ->searchable(),
                TextColumn::make('label')
                    ->label('Название')
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('permissions.name')
                    ->label('Права доступа')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip('Удалить'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
