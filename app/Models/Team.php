<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatusEnum;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ward_id',
        'name',
        'leader_name',
        'team_leader_id',
        'contact_number',
        'status'
    ];

    protected $casts = [
        'status' => ActiveStatusEnum::class,
    ];

    // Relationships
    public function teamLeader()
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function corporation()
    {
        return $this->hasOneThrough(Corporation::class, Ward::class, 'id', 'id', 'ward_id', 'corporation_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members', 'team_id', 'user_id')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }

    public function surveyors()
    {
        return $this->members()->where('role', 'surveyor');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ActiveStatusEnum::ACTIVE);
    }

    public function scopeByTeamLeader($query, $teamLeaderId)
    {
        return $query->where('team_leader_id', $teamLeaderId);
    }

    public function scopeByWard($query, $wardId)
    {
        return $query->where('ward_id', $wardId);
    }
}