<?php

declare(strict_types=1);

namespace Modules\AccessControl\Models;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Permission as SpatiePermission;

class PermissionModel extends SpatiePermission implements HasLabel
{
    use HasUuids;

    public function getLabel(): string|Htmlable|null
    {
        return trim("[$this->name] $this->label");
    }
}
