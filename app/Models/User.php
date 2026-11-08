<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // Hanya karyawan yang boleh masuk panel admin.
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'karyawan';
    }

    // Kolom di tabel users bernama "nama", bukan "name".
    public function getFilamentName(): string
    {
        return $this->nama;
    }

    public function isPemilik(): bool
    {
        return $this->role === 'pemilik';
    }
}
