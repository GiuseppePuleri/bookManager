<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_copy_id',
        'reserved_at',
        'due_date',
        'returned_at',
        'status',
        'extended_count',
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'reserved_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    //Boot method to set default values.
    protected static function boot(){
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->reserved_at)) {
                $reservation->reserved_at = now();
            }
            
            // Default due date: 14 days from reservation
            if (empty($reservation->due_date)) {
                $reservation->due_date = now()->addDays(14);
            }
        });
    }

    //Get the user that owns the reservation.
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function bookCopy(){
        return $this->belongsTo(BookCopy::class);
    }

    public function isActive(){
        return $this->status === 'active';
    }

    public function isOverdue(){
        return $this->isActive() && $this->due_date < now();
    }

    public function getDaysUntilDueAttribute(){
        if (!$this->isActive()) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    public function getDaysOverdueAttribute(){
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }

    public function getStatusBadgeClassAttribute(){
        $classes = [
            'active' => 'badge-success',
            'completed' => 'badge-info',
            'cancelled' => 'badge-danger',
        ];

        return $classes[$this->status] ?? 'badge-secondary';
    }

    public function getStatusLabelAttribute(){
        $labels = [
            'active' => 'Attiva',
            'completed' => 'Completata',
            'cancelled' => 'Annullata',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function extend($days = 7){
        if (!$this->isActive()) {
            return false;
        }

        $this->due_date = $this->due_date->addDays($days);
        $this->extended_count++;
        $this->save();

        return true;
    }

    public function complete(){
        if (!$this->isActive()) {
            return false;
        }

        $this->status = 'completed';
        $this->returned_at = now();
        
        // Update book copy status to available
        $this->bookCopy->update(['status' => 'available']);
        
        $this->save();

        return true;
    }

    public function cancel(){
        if (!$this->isActive()) {
            return false;
        }

        $this->status = 'cancelled';
        
        // Update book copy status to available
        $this->bookCopy->update(['status' => 'available']);
        
        $this->save();

        return true;
    }

    //Scope a query to only include active reservations.
    public function scopeActive($query){
        return $query->where('status', 'active');
    }

    //Scope a query to only include overdue reservations.
    public function scopeOverdue($query){
        return $query->where('status', 'active')
                     ->where('due_date', '<', now());
    }

    //Scope a query to only include completed reservations.
    public function scopeCompleted($query){
        return $query->where('status', 'completed');
    }
}