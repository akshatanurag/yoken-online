<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebinarRegistration extends Model
{
    protected $guarded = [];
    //
    public function webinar()
    {
        return $this->belongsTo('\App\Webinar');
    }
    public function payment()
    {
        return $this->hasOne('\App\WebinarPayment');
    }
    public function user()
    {
        return $this->belongsTo('\App\User');
    }
}
