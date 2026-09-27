<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.magazine', 'layouts.app', 'layouts.guest', 'layouts.admin'], function ($view) {
            $settings = Schema::hasTable('site_settings') ? DB::table('site_settings')->pluck('value', 'key') : collect();
            $view->with('siteSettings', $settings);
        });

        View::composer('layouts.magazine', function ($view) {
            $categories = Schema::hasTable('categories') && Schema::hasColumn('categories', 'parent_id')
                ? Category::with('children')->whereNull('parent_id')->whereHas('children')->orderBy('name')->get()
                : collect();
            $view->with('navigationCategories', $categories);
        });
    }
}
