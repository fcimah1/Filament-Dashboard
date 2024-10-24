<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'city',
        'zip_code',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // public function getAgeAttribute()
    // {
    //     return \Carbon\Carbon::parse($this->date_of_birth)->age;
    // }
}
