<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MisDynamic extends Model
{
    use HasFactory;

    protected $guarded = []; // allow all fields for mass assignment

    /**
     * ✅ Dynamically set table name at runtime
     */
    public function setTableName($tableName)
    {
        $this->table = $tableName;
        return $this;
    }

    /**
     * ✅ Corporation relationship
     */
    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }
}
