<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.partials.sidebar', function ($view) {
            $menus = collect();

            if (Auth::check()) {
                $menus = Auth::user()->accessibleMenus();
            }

            $view->with('sidebarMenus', $menus);
        });
    }
}