<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hardware extends Model
{
    use HasFactory;

    protected $table = 'hardwares';

    protected $fillable = [
        'name',
        'status',
        'suhu',
        'kelembapan_udara',
        'kelembapan_tanah'
    ];
}
