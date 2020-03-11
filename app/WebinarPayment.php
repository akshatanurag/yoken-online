<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebinarPayment extends Model
{
    protected $casts = [
        'payment_details' => 'array',
    ];
    protected $guarded = [];
    //
    public function registration()
    {
        return $this->belongsTo('\App\WebinarRegistration');
    }
}
