<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'pegawaifk',
        'groupfk',
        'username',
        'password',
        'is_superadmin',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_superadmin' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawaifk');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupfk');
    }

    public function menuOverrides(): HasMany
    {
        return $this->hasMany(UserMenu::class, 'user_id');
    }

    public function accessibleMenus()
    {
        if ($this->is_superadmin) {
            return Menu::topLevel()->with(['children' => function ($q) {
                $q->orderBy('nourut');
            }])->orderBy('nourut')->get();
        }

        $groupMenuIds = GroupMenu::where('group_id', $this->groupfk)->pluck('menu_id');
        $userOverrides = UserMenu::where('user_id', $this->id)->get()->keyBy('menu_id');

        $allowedIds = $groupMenuIds->toArray();

        foreach ($userOverrides as $menuId => $override) {
            if ($override->is_granted) {
                $allowedIds[] = $menuId;
            } else {
                $allowedIds = array_diff($allowedIds, [$menuId]);
            }
        }

        $allowedIds = array_unique($allowedIds);

        return Menu::active()
            ->topLevel()
            ->orderBy('nourut')
            ->get()
            ->filter(function ($menu) use ($allowedIds) {
                $childIds = $menu->allChildren()->pluck('id')->toArray();
                return in_array($menu->id, $allowedIds) || count(array_intersect($childIds, $allowedIds)) > 0;
            })
            ->map(function ($menu) use ($allowedIds) {
                $menu->setRelation('children', $menu->allChildren()
                    ->orderBy('nourut')
                    ->get()
                    ->filter(fn ($child) => in_array($child->id, $allowedIds))
                    ->values());
                return $menu;
            })
            ->values();
    }
}