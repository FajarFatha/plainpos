<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branch_m';

    protected $fillable = [
        'nama_branch',
        'lokasi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'branch');
    }

    public function stokBranch(): HasMany
    {
        return $this->hasMany(StokBranch::class, 'branch_id');
    }
}