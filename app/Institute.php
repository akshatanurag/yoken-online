<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Institute extends Authenticatable
{
    use Notifiable;
    protected $guard = "institute";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','phone', 'password', 'description', 'state', 'city', 'location',
        'address', 'affiliation', 'no_of_students', 'logo_file', 'display_pic_links', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function courses()
    {
        return $this->hasMany('\App\Course');
    }
}
