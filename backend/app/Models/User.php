<?php

namespace App\Models;

// Import library yang dibutuhin buat login & token
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // WAJIB ADA: HasApiTokens supaya bisa bikin token (createToken)
    use HasApiTokens, Notifiable;

    /**
     * Kolom yang boleh diisi (Mass Assignable)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Kolom yang disembunyikan saat data dikirim ke React
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Otomatis ngerubah password jadi format hash saat disimpan
     */
    protected $casts = [
        'password' => 'hashed',
    ];
}
