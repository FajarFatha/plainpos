<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProdukRequest;
use App\Models\Produk;
use App\Models\StokBranch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    private const DEFAULT_BRANCH = 1;

    public function index()
    {
        return view('admin.produk.index');
    }

    public function data(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $searchValue = $request->input('search.value', '');

        $columns = ['nama_produk', 'harga_jual', 'stok', 'is_active'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'nama_produk';

        $query = Produk::query()
            ->leftJoin('stok_branch', function ($join) {
                $join->on('stok_branch.produk_id', '=', 'produk_m.id')
                    ->where('stok_branch.branch_id', '=', self::DEFAULT_BRANCH)
                    ->whereNull('stok_branch.deleted_at');
            })
            ->select([
                'produk_m.id',
                'produk_m.nama_produk',
                'produk_m.gambar',
                'produk_m.harga_jual',
                'produk_m.is_active',
                DB::raw('COALESCE(stok_branch.stok, 0) as stok'),
            ]);

        $totalRecords = (clone $query)->count();

        if (! empty($searchValue)) {
            $query->where('produk_m.nama_produk', 'like', "%{$searchValue}%");
        }

        $filteredRecords = (clone $query)->count();

        $mapOrderColumn = [
            'nama_produk' => 'produk_m.nama_produk',
            'harga_jual' => 'produk_m.harga_jual',
            'stok' => 'stok',
            'is_active' => 'produk_m.is_active',
        ];

        $query->orderBy($mapOrderColumn[$orderColumn] ?? 'produk_m.nama_produk', $orderDir);

        $rows = $query->skip($start)->take($length)->get();

        $data = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'nama_produk' => $row->nama_produk,
                'gambar_url' => $row->gambar ? Storage::disk('public')->url($row->gambar) : null,
                'harga_jual' => (float) $row->harga_jual,
                'stok' => (int) $row->stok,
                'is_active' => (bool) $row->is_active,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function show(Produk $produk): JsonResponse
    {
        $stok = $produk->stokDiBranch(self::DEFAULT_BRANCH)->first();

        return response()->json([
            'id' => $produk->id,
            'nama_produk' => $produk->nama_produk,
            'gambar_url' => $produk->gambar_url,
            'harga_jual' => (float) $produk->harga_jual,
            'stok' => $stok->stok,
            'is_active' => $produk->is_active,
        ]);
    }

    public function store(ProdukRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $gambarPath = null;

        try {
            DB::beginTransaction();

            if ($request->hasFile('gambar')) {
                $gambarPath = $request->file('gambar')->store('produk', 'public');
            }

            $produk = Produk::create([
                'nama_produk' => $validated['nama_produk'],
                'gambar' => $gambarPath,
                'harga_jual' => $validated['harga_jual'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            StokBranch::create([
                'produk_id' => $produk->id,
                'branch_id' => self::DEFAULT_BRANCH,
                'stok' => $validated['stok'] ?? 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Produk \"{$produk->nama_produk}\" berhasil ditambahkan.",
            ]);
        } catch (\Throwable $e) {
            if ($gambarPath) {
                Storage::disk('public')->delete($gambarPath);
            }

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan produk: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(ProdukRequest $request, Produk $produk): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $gambarPath = $produk->gambar;
            $oldGambarPath = null;

            if ($request->hasFile('gambar')) {
                $oldGambarPath = $produk->gambar;
                $gambarPath = $request->file('gambar')->store('produk', 'public');
            }

            $produk->update([
                'nama_produk' => $validated['nama_produk'],
                'gambar' => $gambarPath,
                'harga_jual' => $validated['harga_jual'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            if (isset($validated['stok'])) {
                StokBranch::updateOrCreate(
                    ['produk_id' => $produk->id, 'branch_id' => self::DEFAULT_BRANCH],
                    ['stok' => $validated['stok']]
                );
            }

            DB::commit();

            if ($oldGambarPath) {
                Storage::disk('public')->delete($oldGambarPath);
            }

            return response()->json([
                'success' => true,
                'message' => "Produk \"{$produk->nama_produk}\" berhasil diperbarui.",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Produk $produk): JsonResponse
    {
        try {
            DB::beginTransaction();

            $nama = $produk->nama_produk;

            $produk->stokBranch()->delete();
            $produk->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Produk \"{$nama}\" berhasil dihapus.",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: '.$e->getMessage(),
            ], 500);
        }
    }

    public function toggleActive(Produk $produk): JsonResponse
    {
        try {
            $newStatus = ! $produk->is_active;
            $produk->update(['is_active' => $newStatus]);

            return response()->json([
                'success' => true,
                'is_active' => $newStatus,
                'message' => $newStatus ? 'Produk diaktifkan.' : 'Produk dinonaktifkan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: '.$e->getMessage(),
            ], 500);
        }
    }
}