<?php

namespace App\Models;

use App\Enums\ActiveStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CorporationUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'corporation_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile',
        'phone',
        'corporation_id',
        'city',
        'gender',
        'date_of_birth',
        'status',
        'storage_path',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    // Helper methods
    public function getProfileUrlAttribute()
    {
        if ($this->profile) {
            return asset($this->profile);
        }
        return null;
    }

    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'team_leader' => 'Team Leader',
            'surveyor' => 'Surveyor',
            'dc' => 'District Commissioner',
            'commissioner' => 'Commissioner',
            default => ucfirst($this->role)
        };
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? 'Active' : 'Inactive';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
