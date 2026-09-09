<?php

declare(strict_types=1);

namespace Modules\Blog\Filament\Resources\ArticleModels\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ArticleModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('id')
                    ->label('ID')
                    ->disabled()
                    ->visible(fn (string $operation): bool => $operation === 'edit')
                    ->copyable(),
                Select::make('user_id')
                    ->label('Автор')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn (string $operation): ?string => $operation === 'create' ? Auth::id() : null),
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->maxLength(255),
                RichEditor::make('text')
                    ->label('Текст')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
