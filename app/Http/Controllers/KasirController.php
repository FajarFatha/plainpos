<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KasirController extends Controller
{
    
    private const DEFAULT_BRANCH = 1;

    public function index()
    {
        return view('transaksi.kasir');
    }
    
    public function produk(Request $request): JsonResponse
    {
        $produk = Produk::query()
            ->leftJoin('stok_branch', function ($join) {
                $join->on('stok_branch.produk_id', '=', 'produk_m.id')
                    ->where('stok_branch.branch_id', '=', self::DEFAULT_BRANCH)
                    ->whereNull('stok_branch.deleted_at');
            })
            ->where('produk_m.is_active', true)
            ->orderBy('produk_m.nama_produk')
            ->get([
                'produk_m.id',
                'produk_m.nama_produk',
                'produk_m.gambar',
                'produk_m.harga_jual',
                'stok_branch.stok',
            ]);

        $data = $produk->map(function ($row) {
            return [
                'id' => $row->id,
                'nama_produk' => $row->nama_produk,
                'gambar_url' => $row->gambar ? Storage::disk('public')->url($row->gambar) : null,
                'harga_jual' => (float) $row->harga_jual,
                'stok' => (int) ($row->stok ?? 0),
            ];
        });

        return response()->json(['data' => $data]);
    }
}