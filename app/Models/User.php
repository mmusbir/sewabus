<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function allowedRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_EDITOR,
            self::ROLE_USER,
        ];
    }

    public static function roleLabels(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_USER => 'User',
        ];
    }

    public function roleLabel(): string
    {
        return self::roleLabels()[$this->role] ?? ucfirst((string) $this->role);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN || $this->isBootstrapAdmin();
    }

    public function hasPanelAccess(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_EDITOR], true)
            || $this->isBootstrapAdmin();
    }

    public function canAccessSettings(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true)
            || $this->isBootstrapAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN || $this->isBootstrapAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->hasPanelAccess();
    }

    private function isBootstrapAdmin(): bool
    {
        $adminEmails = config('auth.admin_emails', []);
        $isBootstrapAdmin = !app()->environment('testing') && $this->id === 1;

        return $isBootstrapAdmin || in_array(strtolower((string) $this->email), $adminEmails, true);
    }

    public function profilePhotoUrl(): string
    {
        if ($this->profile_photo_path) {
            return Storage::disk('public')->url($this->profile_photo_path);
        }

        return '/stitch_admin_avatar.jpg';
    }
}
