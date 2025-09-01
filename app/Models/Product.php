<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'product_number',
        'image_url',
        'price',
        'discount',
        'manufacturing_material',
        'manufacturing_country',
        'stock_quantity',
        'is_available',
        'category_id'
    ];

    protected $appends = ['final_price'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    protected function finalPrice(): Attribute
    {
        return new Attribute(
            get: fn () => $this->price - ($this->price * ($this->discount / 100)),
        );
    }
}
