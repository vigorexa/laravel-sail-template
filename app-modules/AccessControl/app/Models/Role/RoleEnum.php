<?php

declare(strict_types=1);

namespace Modules\AccessControl\Models\Role;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

enum RoleEnum: string implements HasLabel
{
    case SUPERUSER = 'SUPERUSER';
    case ADMIN = 'ADMIN';

    public function getLabel(): string|Htmlable|null
    {
        return Str::title($this->value);
    }

    public function roleModel(): Model
    {
        return RoleModel::find($this->value);
    }
}
