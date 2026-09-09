<?php

declare(strict_types=1);

namespace Modules\AccessControl\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\AccessControl\Database\Factories\UserFactory;
use Modules\AccessControl\Models\Role\RoleEnum;
use Modules\AccessControl\Policies\UserPolicy;
use Spatie\Permission\Traits\HasRoles;

#[UsePolicy(UserPolicy::class)]
class UserModel extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use HasUuids;
    use Notifiable;

    protected $table = 'acl_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected string $guard_name = 'web';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Filament

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole([RoleEnum::SUPERUSER, RoleEnum::ADMIN]);
    }

    // Permissions

    protected function getDefaultGuardName(): string
    {
        return $this->guard_name;
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
