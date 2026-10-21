<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'menu_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image',
        'rating',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'stock' => 'integer',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderItem::class, 'menu_id', 'menu_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('assets/images/default-food.png');
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }
        // If image was saved from native Cimohot (e.g. assets/uploads/...)
        return asset($this->image);
    }
}
