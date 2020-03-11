<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = [];
    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }

    public function institute()
    {
        return $this->belongsTo('\App\Institute');
    }

    public function faculties()
    {
        return $this->hasMany('App\Faculty');
    }

    public function batches ()
    {
        return $this->hasMany('\App\Batch');
    }

    public function installments()
    {
        return $this->hasMany('\App\Installment');
    }
    public function faqs()
    {
        return $this->hasMany('\App\Faq');
    }
    public function scopeCategory($query, $categories)
    {
        return $query->whereHas('categories', function ($query) use ($categories){
            $query->whereIn('category_id',$categories);
        });
    }
    public function scopeLocation($query, $locations)
    {
        //return $query;
        //dd($query->join('institutes', 'courses.institute_id', 'institutes.id')->whereIn('location', $locations));
        return $query->select('courses.*')->join('institutes', 'courses.institute_id', 'institutes.id')->whereIn('location', $locations);
    }

    public function enrollments()
    {
        return $this->hasMany('\App\Enrollment');
    }

    public function resource()
    {
        return $this->hasMany('\App\Resource');
    }
}
