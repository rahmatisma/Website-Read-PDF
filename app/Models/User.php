<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property bool $is_verified_by_admin
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property string|null $avatar
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $initials
 * @property-read string $role_badge_variant
 * @property-read string $role_name
 * @property-read string $verification_status
 * @property-read string $verification_status_color
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read User|null $verifiedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $verifiedUsers
 * @property-read int|null $verified_users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User engineers()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User nms()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User pendingVerification()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role(string $role)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsVerifiedByAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerifiedBy($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'email_verified_at',
        'is_verified_by_admin',
        'verified_by',
        'verified_at',
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
            'verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified_by_admin' => 'boolean',
        ];
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Relasi ke admin yang memverifikasi user ini
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relasi ke user-user yang diverifikasi oleh admin ini
     */
    public function verifiedUsers()
    {
        return $this->hasMany(User::class, 'verified_by');
    }

    // ========================================
    // ROLE CHECKERS
    // ========================================

    /**
     * Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Engineer
     */
    public function isEngineer(): bool
    {
        return $this->role === 'engineer';
    }

    /**
     * Check if user is NMS
     */
    public function isNms(): bool
    {
        return $this->role === 'nms';
    }

    // ========================================
    // VERIFICATION CHECKERS
    // ========================================

    /**
     * Check if user is verified by admin
     */
    public function isVerifiedByAdmin(): bool
    {
        return $this->is_verified_by_admin === true;
    }

    /**
     * Check if user can login
     * User hanya bisa login jika sudah diverifikasi oleh admin
     */
    public function canLogin(): bool
    {
        return $this->is_verified_by_admin === true;
    }

    /**
     * Check if user is pending verification
     */
    public function isPendingVerification(): bool
    {
        return $this->is_verified_by_admin === false;
    }

    // ========================================
    // ACCESSORS (untuk UI)
    // ========================================

    /**
     * Get badge variant for role (untuk UI)
     */
    public function getRoleBadgeVariantAttribute(): string
    {
        return match($this->role) {
            'admin' => 'destructive',
            'engineer' => 'default',
            'nms' => 'secondary',
            default => 'outline',
        };
    }

    /**
     * Get role display name
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'engineer' => 'Engineer',
            'nms' => 'Network Management System',
            default => ucfirst($this->role),
        };
    }

    /**
     * Get verification status text
     */
    public function getVerificationStatusAttribute(): string
    {
        if ($this->is_verified_by_admin) {
            return 'Verified';
        }
        return 'Pending Approval';
    }

    /**
     * Get verification status color (untuk badge)
     */
    public function getVerificationStatusColorAttribute(): string
    {
        return $this->is_verified_by_admin ? 'green' : 'yellow';
    }

    // ========================================
    // SCOPES (untuk query builder)
    // ========================================

    /**
     * Scope: Filter user yang sudah verified
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified_by_admin', true);
    }

    /**
     * Scope: Filter user yang pending verification
     */
    public function scopePendingVerification($query)
    {
        return $query->where('is_verified_by_admin', false);
    }

    /**
     * Scope: Filter by role
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope: Filter admin only
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope: Filter engineer only
     */
    public function scopeEngineers($query)
    {
        return $query->where('role', 'engineer');
    }

    /**
     * Scope: Filter NMS only
     */
    public function scopeNms($query)
    {
        return $query->where('role', 'nms');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Verify user (dipanggil oleh admin)
     */
    public function verify(User $admin): void
    {
        $this->update([
            'is_verified_by_admin' => true,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);
    }

    /**
     * Revoke verification
     */
    public function revokeVerification(): void
    {
        $this->update([
            'is_verified_by_admin' => false,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    /**
     * Get initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($this->name, 0, 2));
    }
}