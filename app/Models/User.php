<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function myWorkspaces()
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    public function connectedWorkspace(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->workspaces()
                ->wherePivot('connected', '=', true)
                ->first(),
        );
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class)
            ->withTimestamps();
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class)
            ->withTimestamps();
    }
}
