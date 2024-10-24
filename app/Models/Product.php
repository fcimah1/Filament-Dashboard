<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'price', 'image',
                        'category_id', 'brand_id', 'quantity', 'featured', 'available'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::url("storage/products$this->image");
        }
    }
    
}
