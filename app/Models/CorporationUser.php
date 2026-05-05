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

    // Accessors & Mutators
    public function getProfileUrlAttribute()
    {
        if ($this->profile) {
            return asset('storage/' . $this->profile);
        }
        return null;
    }

    public function getRoleLabelAttribute()
    {
        return ucfirst($this->role);
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === ActiveStatusEnum::ACTIVE->value ? 'Active' : 'Inactive';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === ActiveStatusEnum::ACTIVE->value
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }
}
