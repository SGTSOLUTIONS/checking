<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Corporation extends Model
{
    use SoftDeletes; // 👈 enables soft delete behavior

    protected $fillable = [
        'name',
        'code',
        'district',
        'state',
        'logo',
        'boundary',
        'status',
    ];

    protected $casts = [
        'boundary' => 'array',
    ];

    // 👇 Optional but recommended if you use soft deletes
    protected $dates = ['deleted_at'];

    public function wards()
    {
        return $this->hasMany(Ward::class);
    }
}
    