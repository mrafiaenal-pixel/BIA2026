<?php

// App\Models\kaApi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kaApi extends Model
{
    use HasFactory;

    // Sesuaikan dengan kolom yang ada di migrasi kamu nanti
    protected $fillable = [
        'user_message',
        'bot_response'
    ];
}