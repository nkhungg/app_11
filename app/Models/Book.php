<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Book extends Model
{
    protected $primaryKey = 'book_id';
    protected $fillable = ['user_id', 'title', 'author', 'category_id', 'price', 'stock', 'isbn', 'description', 'image'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (Auth::check() && !$book->user_id) {
                $book->user_id = Auth::id();
            }
        });
    }
}
