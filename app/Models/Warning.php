<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_unit',
        'title',
        'status',
        'created_at',
        'photos',
    ];
}
