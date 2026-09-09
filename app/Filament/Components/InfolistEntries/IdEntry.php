<?php

declare(strict_types=1);

namespace App\Filament\Components\InfolistEntries;

use Filament\Infolists\Components\TextEntry;
use Filament\Support\Icons\Heroicon;

class IdEntry
{
    public static function make(string $name = 'id'): TextEntry
    {
        return TextEntry::make($name)
            ->label('ID')
            ->icon(Heroicon::ClipboardDocumentList)
            ->copyable();
    }
}
