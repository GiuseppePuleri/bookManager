<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BookCopy extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'barcode',
        'condition',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'condition' => 'string',
        'status' => 'string',
    ];

    //Boot method to generate barcode automatically.
    protected static function boot(){
        parent::boot();

        static::creating(function ($bookCopy) {
            if (empty($bookCopy->barcode)) {
                $bookCopy->barcode = self::generateUniqueBarcode();
            }
        });
    }

    public static function generateUniqueBarcode(){
        do {
            // Formato: BOOK-YYYYMMDD-XXXX (es: BOOK-20250127-A3F9)
            $barcode = 'BOOK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    //Get the book that owns the copy.
    public function book(){
        return $this->belongsTo(Book::class);
    }

    //Get condition label in Italian.
    public function getConditionLabelAttribute(){
        $labels = [
            'very good' => 'Ottimo',
            'good' => 'Buono',
            'bad' => 'Discreto',
        ];

        return $labels[$this->condition] ?? $this->condition;
    }

    //Get status label in Italian.
    public function getStatusLabelAttribute(){
        $labels = [
            'available' => 'Disponibile',
            'reserved' => 'Prenotato',
            'loaned' => 'In prestito',
            'maintenance' => 'Manutenzione',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute(){
        $classes = [
            'available' => 'badge-success',
            'reserved' => 'badge-warning',
            'loaned' => 'badge-info',
            'maintenance' => 'badge-danger',
        ];

        return $classes[$this->status] ?? 'badge-secondary';
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation(){
        return $this->hasOne(Reservation::class)->where('status', 'active');
    }

}