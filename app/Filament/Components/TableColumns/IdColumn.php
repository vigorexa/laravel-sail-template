<?php

declare(strict_types=1);

namespace App\Filament\Components\TableColumns;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;

class IdColumn
{
    public static function make(string $name = 'id'): TextColumn
    {
        return TextColumn::make($name)
            ->label('ID')
            ->icon(Heroicon::ClipboardDocumentList)
            ->searchable()
            ->copyable()
            ->toggleable(isToggledHiddenByDefault: true);
    }
}
