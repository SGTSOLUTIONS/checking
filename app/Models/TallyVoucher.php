<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TallyVoucher extends Model
{
    protected $fillable = [
        'tally_guid', 'voucher_number', 'voucher_type',
        'date', 'party_name', 'narration', 'ledgers', 'raw_xml'
    ];

    protected $casts = [
        'ledgers' => 'array',
        'date' => 'date',
    ];
}
