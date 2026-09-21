<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        return view('admin.branch.index');
    }

    public function data(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $searchValue = $request->input('search.value', '');

        $columns = ['nama_branch', 'lokasi', 'is_active'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'nama_branch';

        $query = Branch::query();

        $totalRecords = (clone $query)->count();

        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('nama_branch', 'like', "%{$searchValue}%")
                    ->orWhere('lokasi', 'like', "%{$searchValue}%");
            });
        }

        $filteredRecords = (clone $query)->count();

        $query->orderBy($orderColumn, $orderDir);

        $rows = $query->skip($start)->take($length)->get();

        $data = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'nama_branch' => $row->nama_branch,
                'lokasi' => $row->lokasi,
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

    public function show(Branch $branch): JsonResponse
    {
        return response()->json([
            'id' => $branch->id,
            'nama_branch' => $branch->nama_branch,
            'lokasi' => $branch->lokasi,
            'is_active' => $branch->is_active,
        ]);
    }

    public function store(BranchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $branch = Branch::create([
                'nama_branch' => $validated['nama_branch'],
                'lokasi' => $validated['lokasi'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cabang \"{$branch->nama_branch}\" berhasil ditambahkan.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan cabang: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(BranchRequest $request, Branch $branch): JsonResponse
    {
        $validated = $request->validated();

        try {
            $branch->update([
                'nama_branch' => $validated['nama_branch'],
                'lokasi' => $validated['lokasi'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cabang \"{$branch->nama_branch}\" berhasil diperbarui.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui cabang: '.$e->getMessage(),
            ], 500);
        }
    }
    
    public function destroy(Branch $branch): JsonResponse
    {
        try {
            $nama = $branch->nama_branch;
            $branch->delete();

            return response()->json([
                'success' => true,
                'message' => "Cabang \"{$nama}\" berhasil dihapus.",
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => "Cabang \"{$branch->nama_branch}\" tidak bisa dihapus karena masih memiliki data pegawai atau stok yang terhubung.",
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus cabang: '.$e->getMessage(),
            ], 500);
        }
    }

    public function toggleActive(Branch $branch): JsonResponse
    {
        try {
            $newStatus = ! $branch->is_active;
            $branch->update(['is_active' => $newStatus]);

            return response()->json([
                'success' => true,
                'is_active' => $newStatus,
                'message' => $newStatus ? 'Cabang diaktifkan.' : 'Cabang dinonaktifkan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: '.$e->getMessage(),
            ], 500);
        }
    }
}