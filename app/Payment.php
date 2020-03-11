<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = [
        'payment_details' => 'array',
    ];
    protected $guarded = [];
    //
    public function enrollment()
    {
        return $this->belongsTo('\App\Enrollment');
    }
}
