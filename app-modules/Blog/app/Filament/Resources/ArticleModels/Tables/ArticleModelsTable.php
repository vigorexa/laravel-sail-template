<?php

declare(strict_types=1);

namespace Modules\Blog\Filament\Resources\ArticleModels\Tables;

use App\Filament\Components\TableColumns\IdColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticleModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Автор')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('text')
                    ->label('Текст')
                    ->limit(80)
                    ->searchable(),
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
