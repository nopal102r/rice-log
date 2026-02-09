<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\View::composer(['components.navbar', 'components.sidebar-boss'], function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                
                // Unread notifications for all users
                $unreadNotificationsCount = \App\Models\Notification::where('user_id', $user->id)
                    ->where('read', false)
                    ->count();
                
                $view->with('unreadNotificationsCount', $unreadNotificationsCount);

                // Boss-specific pending counts
                if ($user->role === 'bos') {
                    $pendingLeaveCount = \App\Models\LeaveSubmission::where('status', 'pending')->count();
                    $pendingDepositCount = \App\Models\Deposit::where('status', 'pending')->count();
                    
                    $view->with('pendingLeaveCount', $pendingLeaveCount);
                    $view->with('pendingDepositCount', $pendingDepositCount);
                }
            }
        });
    }
}
