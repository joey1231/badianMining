<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $user = Auth::user();

            if (!is_null($user)) {
                $is_owner = $user->roles()->where('name', 'owner')->count();
                $is_investor = $user->roles()->where('name', 'investor')->count();
                $is_Admin = $user->roles()->where('name', 'admin')->count();
                $view->with('isInvestor', $is_investor > 0);
                $view->with('isOwner', $is_owner > 0);
                $view->with('isAdmin', $is_Admin > 0);
            }

        });
    }
}
