<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * @return array<string, string>
     */
    protected function casts(): array{
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function activeReservations(){
        return $this->hasMany(Reservation::class)->where('status', 'active');
    }

    public function completedReservations(){
        return $this->hasMany(Reservation::class)->where('status', 'completed');
    }

    public function isAdmin(){
        return $this->role === 'admin';
    }

    public function isUser(){
        return $this->role === 'user';
    }

    public function getRoleBadgeClassAttribute(){
        return $this->role === 'admin' ? 'badge-danger' : 'badge-secondary';
    }

    public function getRoleLabelAttribute(){
        return $this->role === 'admin' ? 'Admin' : 'Utente';
    }

    //Scope a query to only include admins.
    public function scopeAdmins($query){
        return $query->where('role', 'admin');
    }

    // Scope a query to only include regular users.
    public function scopeUsers($query){
        return $query->where('role', 'user');
    }
}