<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Webinar extends Model
{
    public function registrations()
    {
        return $this->hasMany('\App\WebinarRegistration');
    }

    public function resource()
    {
        return $this->hasMany('\App\Resource');
    }
}
