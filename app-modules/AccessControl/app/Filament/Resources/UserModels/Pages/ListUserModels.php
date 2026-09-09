<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\UserModels\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\AccessControl\Filament\Resources\UserModels\UserModelResource;

class ListUserModels extends ListRecords
{
    protected static string $resource = UserModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
