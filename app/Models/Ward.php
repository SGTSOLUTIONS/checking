<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ward extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'corporation_id',
        'ward_no',
        'drone_image',
        'extent_left',
        'extent_right',
        'extent_top',
        'extent_bottom',
        'boundary',
        'zone',
        'status',
    ];

    protected $casts = [
        'boundary' => 'array',
    ];

    protected $dates = ['deleted_at']; // 👈 optional clarity for timestamp

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
