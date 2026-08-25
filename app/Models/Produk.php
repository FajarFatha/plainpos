<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk_m';

    protected $fillable = [
        'nama_produk',
        'gambar',
        'harga_jual',
        'is_active',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    public function stokBranch(): HasMany
    {
        return $this->hasMany(StokBranch::class, 'produk_id');
    }
    
    public function stokDiBranch(int $branchId): HasOne
    {
        return $this->hasOne(StokBranch::class, 'produk_id')
            ->where('branch_id', $branchId)
            ->withDefault(['stok' => 0, 'branch_id' => $branchId]);
    }
    
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return Storage::disk('public')->url($this->gambar);
    }
}