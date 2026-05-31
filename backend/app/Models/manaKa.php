<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class manaKa extends Model
{
    use HasFactory;
    protected $fillable = [
        "suhu",
        "ketSuhu",
        "lembap",
        "ketLembap"
    ];
}
