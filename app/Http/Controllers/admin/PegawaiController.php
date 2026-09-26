<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PegawaiUserRequest;
use App\Models\Group;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PegawaiController extends Controller
{
    private const DEFAULT_BRANCH = 1;

    public function index()
    {
        $groups = Group::where('is_active', true)->orderBy('nama_group')->get();

        return view('admin.pegawai.index', compact('groups'));
    }

    public function data(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $searchValue = $request->input('search.value', '');

        $columns = ['nip', 'nama_pegawai', 'jenis_kelamin', 'username', 'nama_group', 'is_active'];
        $orderColumnIndex = (int) $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'nama_pegawai';

        $query = Pegawai::query()
            ->leftJoin('users', function ($join) {
                $join->on('users.pegawaifk', '=', 'pegawai_m.id')
                    ->whereNull('users.deleted_at');
            })
            ->leftJoin('groups_m', function ($join) {
                $join->on('groups_m.id', '=', 'users.groupfk')
                    ->whereNull('groups_m.deleted_at');
            })
            ->select([
                'pegawai_m.id as pegawai_id',
                'pegawai_m.nip',
                'pegawai_m.nama_pegawai',
                'pegawai_m.jenis_kelamin',
                'pegawai_m.is_active as pegawai_is_active',
                'users.id as user_id',
                'users.username',
                'users.is_active as user_is_active',
                'users.is_superadmin',
                'groups_m.nama_group',
            ]);

        $totalRecords = (clone $query)->count();

        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('pegawai_m.nip', 'like', "%{$searchValue}%")
                    ->orWhere('pegawai_m.nama_pegawai', 'like', "%{$searchValue}%")
                    ->orWhere('users.username', 'like', "%{$searchValue}%")
                    ->orWhere('groups_m.nama_group', 'like', "%{$searchValue}%");
            });
        }

        $filteredRecords = (clone $query)->count();

        $mapOrderColumn = [
            'nip' => 'pegawai_m.nip',
            'nama_pegawai' => 'pegawai_m.nama_pegawai',
            'jenis_kelamin' => 'pegawai_m.jenis_kelamin',
            'username' => 'users.username',
            'nama_group' => 'groups_m.nama_group',
            'is_active' => 'pegawai_m.is_active',
        ];

        $query->orderBy($mapOrderColumn[$orderColumn] ?? 'pegawai_m.nama_pegawai', $orderDir);

        $rows = $query->skip($start)->take($length)->get();

        $data = $rows->map(function ($row) {
            return [
                'pegawai_id' => $row->pegawai_id,
                'nip' => $row->nip,
                'nama_pegawai' => $row->nama_pegawai,
                'jenis_kelamin' => $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                'username' => $row->username ?? '-',
                'nama_group' => $row->nama_group ?? '-',
                'is_superadmin' => (bool) $row->is_superadmin,
                'is_active' => (bool) $row->pegawai_is_active && (bool) $row->user_is_active,
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function show(Pegawai $pegawai): JsonResponse
    {
        $pegawai->load('users');
        $user = $pegawai->users->first();

        return response()->json([
            'pegawai' => [
                'id' => $pegawai->id,
                'nip' => $pegawai->nip,
                'nama_pegawai' => $pegawai->nama_pegawai,
                'jenis_kelamin' => $pegawai->jenis_kelamin,
                'is_active' => $pegawai->is_active,
            ],
            'user' => $user ? [
                'id' => $user->id,
                'username' => $user->username,
                'groupfk' => $user->groupfk,
                'is_superadmin' => $user->is_superadmin,
                'is_active' => $user->is_active,
            ] : null,
        ]);
    }

    public function store(PegawaiUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $pegawai = Pegawai::create([
                'nip' => Pegawai::generateNip(),
                'nama_pegawai' => $validated['nama_pegawai'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'branch' => self::DEFAULT_BRANCH,
                'is_active' => $request->boolean('pegawai_is_active', true),
            ]);

            User::create([
                'pegawaifk' => $pegawai->id,
                'groupfk' => $validated['groupfk'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'is_superadmin' => $request->boolean('is_superadmin', false),
                'is_active' => $request->boolean('user_is_active', true),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Pegawai \"{$pegawai->nama_pegawai}\" beserta akunnya berhasil ditambahkan. NIP: {$pegawai->nip}",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(PegawaiUserRequest $request, Pegawai $pegawai): JsonResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $pegawai->update([
                'nama_pegawai' => $validated['nama_pegawai'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'is_active' => $request->boolean('pegawai_is_active', true),
            ]);

            $user = $pegawai->users()->first();

            $userData = [
                'groupfk' => $validated['groupfk'],
                'username' => $validated['username'],
                'is_superadmin' => $request->boolean('is_superadmin', false),
                'is_active' => $request->boolean('user_is_active', true),
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if ($user) {
                $user->update($userData);
            } else {
                $userData['pegawaifk'] = $pegawai->id;
                $userData['password'] = Hash::make($validated['password'] ?? Str::random(10));
                User::create($userData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Data \"{$pegawai->nama_pegawai}\" berhasil diperbarui.",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Pegawai $pegawai): JsonResponse
    {
        try {
            DB::beginTransaction();

            $nama = $pegawai->nama_pegawai;

            $pegawai->users()->delete();
            $pegawai->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Data \"{$nama}\" berhasil dihapus.",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: '.$e->getMessage(),
            ], 500);
        }
    }

    public function toggleActive(Pegawai $pegawai): JsonResponse
    {
        try {
            DB::beginTransaction();

            $newStatus = ! $pegawai->is_active;
            $pegawai->update(['is_active' => $newStatus]);
            $pegawai->users()->update(['is_active' => $newStatus]);

            DB::commit();

            return response()->json([
                'success' => true,
                'is_active' => $newStatus,
                'message' => $newStatus ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: '.$e->getMessage(),
            ], 500);
        }
    }
}