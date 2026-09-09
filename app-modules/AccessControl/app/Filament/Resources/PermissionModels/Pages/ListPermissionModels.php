<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\PermissionModels\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\AccessControl\Filament\Resources\PermissionModels\PermissionModelResource;

class ListPermissionModels extends ListRecords
{
    protected static string $resource = PermissionModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
