<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai_m';

    protected $fillable = [
        'nip',
        'nama_pegawai',
        'jenis_kelamin',
        'branch',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'pegawaifk');
    }
    
    public static function generateNip(): string
    {
        $year = now()->format('Y');

        $lastNip = self::where('nip', 'like', "{$year}%")
            ->orderByDesc('nip')
            ->value('nip');

        if ($lastNip) {
            $lastSequence = (int) substr($lastNip, strlen($year));
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        return $year.str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch');
    }
}