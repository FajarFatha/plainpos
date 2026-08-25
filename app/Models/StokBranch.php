<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokBranch extends Model
{
    protected $table = 'stok_branch';

    protected $fillable = [
        'produk_id',
        'branch_id',
        'stok',
    ];

    protected $casts = [
        'stok' => 'integer',
        'branch_id' => 'integer',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
    
}