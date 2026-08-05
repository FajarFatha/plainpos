<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu_m';

    protected $fillable = [
        'head',
        'realname',
        'description',
        'name',
        'route',
        'icon',
        'span1',
        'span2',
        'nourut',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'nourut' => 'integer',
    ];
    
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'head');
    }
    
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'head')
            ->where('is_active', true)
            ->orderBy('nourut');
    }
    
    public function allChildren(): HasMany
    {
        return $this->hasMany(Menu::class, 'head')->orderBy('nourut');
    }
    
    public function scopeTopLevel($query)
    {
        return $query->whereNull('head');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}