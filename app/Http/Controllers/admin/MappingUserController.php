<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\User;
use App\Models\UserMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MappingUserController extends Controller
{
    public function index()
    {
        return view('admin.mapping-user.index');
    }

    public function users(Request $request): JsonResponse
    {
        $search = $request->input('q', '');

        $users = User::query()
            ->with(['pegawai', 'group'])
            ->when($search, function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhereHas('pegawai', function ($sub) use ($search) {
                        $sub->where('nama_pegawai', 'like', "%{$search}%");
                    });
            })
            ->orderBy('username')
            ->limit(50)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'label' => ($user->pegawai->nama_pegawai ?? $user->username)." ({$user->username}) / ".($user->group->nama_group ?? '-'),
                    'group_name' => $user->group->nama_group ?? '-',
                ];
            });

        return response()->json($users);
    }

    public function menus(User $user): JsonResponse
    {
        $grantedMenuIds = UserMenu::where('user_id', $user->id)
            ->where('is_granted', true)
            ->pluck('menu_id')
            ->toArray();

        $topLevelMenus = Menu::active()
            ->topLevel()
            ->orderBy('nourut')
            ->get();

        $tree = $topLevelMenus->map(function ($menu) use ($grantedMenuIds) {
            return $this->buildMenuNode($menu, $grantedMenuIds);
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'group_name' => $user->group->nama_group ?? '-',
            ],
            'menus' => $tree,
            'total_checked' => count($grantedMenuIds),
        ]);
    }

    private function buildMenuNode(Menu $menu, array $grantedMenuIds): array
    {
        $children = $menu->allChildren()
            ->where('is_active', true)
            ->orderBy('nourut')
            ->get();

        return [
            'id' => $menu->id,
            'realname' => $menu->realname,
            'name' => $menu->name,
            'icon' => $menu->icon,
            'checked' => in_array($menu->id, $grantedMenuIds),
            'children' => $children->map(function ($child) use ($grantedMenuIds) {
                return $this->buildMenuNode($child, $grantedMenuIds);
            })->values(),
        ];
    }

    public function save(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:menu_m,id'],
        ]);

        $menuIds = $validated['menu_ids'] ?? [];

        try {
            UserMenu::where('user_id', $user->id)->delete();

            foreach ($menuIds as $menuId) {
                $existing = UserMenu::withTrashed()
                    ->where('user_id', $user->id)
                    ->where('menu_id', $menuId)
                    ->first();

                if ($existing) {
                    $existing->restore();
                    $existing->update(['is_granted' => true]);
                } else {
                    UserMenu::create([
                        'user_id' => $user->id,
                        'menu_id' => $menuId,
                        'is_granted' => true,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Hak akses untuk \"{$user->username}\" berhasil disimpan (".count($menuIds)." menu).",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hak akses: '.$e->getMessage(),
            ], 500);
        }
    }
}