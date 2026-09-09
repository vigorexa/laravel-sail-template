<?php

declare(strict_types=1);

namespace Modules\AccessControl\Filament\Resources\RoleModels\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\AccessControl\Filament\Resources\RoleModels\RoleModelResource;

class ListRoleModels extends ListRecords
{
    protected static string $resource = RoleModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
