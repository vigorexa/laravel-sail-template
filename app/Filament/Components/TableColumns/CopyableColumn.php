<?php

declare(strict_types=1);

namespace App\Filament\Components\TableColumns;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;

class CopyableColumn
{
    public static function make(?string $name): TextColumn
    {
        return TextColumn::make($name)
            ->icon(Heroicon::ClipboardDocumentList)
            ->copyable();
    }
}
