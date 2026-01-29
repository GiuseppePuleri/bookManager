<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'isbn',
        'description',
        'author',
        'publisher',
        'year',
        'category_id',
        'cover_image',
    ];

    //Get the category that owns the book.
    public function category(){
        return $this->belongsTo(Category::class);
    }

    //Get the copies for the book.
    public function copies(){
        return $this->hasMany(BookCopy::class);
    }

    //Get available copies count.
    public function getAvailableCopiesCountAttribute(){
        return $this->copies()->where('status', 'available')->count();
    }

    //Get reserved copies count.
    public function getReservedCopiesCountAttribute(){
        return $this->copies()->where('status', 'reserved')->count();
    }

    //Get loaned copies count.
    public function getLoanedCopiesCountAttribute(){
        return $this->copies()->where('status', 'loaned')->count();
    }


    public function getTotalCopiesCountAttribute(){
        return $this->copies()->count();
    }

    //Get cover image URL.
    public function getCoverUrlAttribute(){
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('storage/covers/default.jpg');
    }

}