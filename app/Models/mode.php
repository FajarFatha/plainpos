<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mode_m';

    protected $fillable = [
        'mode',
        'penjelasan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}