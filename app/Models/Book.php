<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'isbn', 'description'];

    public function getCoverUrlAttribute(): string
    {
        return asset('images/books/'.\Illuminate\Support\Str::slug($this->title).'.jpg');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }
}