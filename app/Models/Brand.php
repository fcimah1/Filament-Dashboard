<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'primary_color',
        'secondary_color',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    
}
