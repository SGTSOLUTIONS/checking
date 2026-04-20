<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'city',
        'phone',
        'date_of_birth',
        'role',
        'profile',
        'status',
        'gender',
        'storage_path'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->storage_path)) {
                $nameSlug = Str::slug($user->name);
                $emailPart = Str::before($user->email, '@');
                $user->storage_path = "user_{$nameSlug}_{$emailPart}_" . time();
            }
        });
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'team_leader_id');
    }
    
    public function leadingTeams()
    {
        return $this->hasMany(Team::class, 'team_leader_id');
    }

    // Check if user is assigned to any team as member
    public function teamMemberships()
    {
        return $this->belongsToMany(Team::class, 'team_members', 'user_id', 'team_id')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }

    // Check if user is assigned to any team
    public function isAssignedToTeam()
    {
        return $this->teamMemberships()->exists();
    }

    // Get the team this user is assigned to (as member)
    public function assignedTeam()
    {
        return $this->teamMemberships()->first();
    }

    /**
     * Get the direct ward access for this surveyor.
     */
    public function directWardAccess(): HasMany
    {
        return $this->hasMany(SurveyorWardAccess::class, 'surveyor_id');
    }

    /**
     * Get the wards this surveyor has direct access to.
     */
    public function accessibleWards(): BelongsToMany
    {
        return $this->belongsToMany(Ward::class, 'surveyor_ward_access', 'surveyor_id', 'ward_id')
                    ->withTimestamps()
                    ->whereNull('surveyor_ward_access.deleted_at');
    }
}