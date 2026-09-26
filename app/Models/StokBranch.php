<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokBranch extends Model
{
    use SoftDeletes;

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
    
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}