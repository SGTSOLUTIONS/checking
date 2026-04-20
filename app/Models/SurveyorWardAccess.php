<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyorWardAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surveyor_ward_access';

    protected $fillable = [
        'surveyor_id',
        'ward_id'
    ];

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }
}