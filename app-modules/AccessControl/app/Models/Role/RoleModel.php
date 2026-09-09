<?php

declare(strict_types=1);

namespace Modules\AccessControl\Models\Role;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleModel extends SpatieRole implements HasLabel
{
    use HasUuids;

    public function getLabel(): string|Htmlable|null
    {
        return trim("[$this->name] $this->label");
    }
}
