<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $guarded = [];
    //
    public function batch()
    {
        return $this->belongsTo('\App\Batch');
    }
    public function payment()
    {
        return $this->hasOne('\App\Payment');
    }
    public function user()
    {
        return $this->belongsTo('\App\User');
    }
}
