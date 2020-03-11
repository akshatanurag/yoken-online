<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    public function webinar()
    {
        return $this->belongsTo('\App\Webinar');
    }

    public function course()
    {
        return $this->belongsTo('\App\Course');
    }
}
