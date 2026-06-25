<?php

namespace App\Providers;

use App\Models\PerangkatWilayah;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
        View::composer('layouts.app', function ($view): void {
            if (! Auth::check()) {
                return;
            }

            $hariIni = Carbon::today();
            $batasPeringatan = Carbon::today()->addDays(90);

            $baseQuery = PerangkatWilayah::query()
                ->where('status', 'aktif')
                ->whereNotNull('akhir_menjabat')
                ->whereDate('akhir_menjabat', '<=', $batasPeringatan);

            $notifications = (clone $baseQuery)
                ->with('wilayah.kecamatan', 'jabatanPerangkat')
                ->orderBy('akhir_menjabat')
                ->limit(5)
                ->get();

            $view->with([
                'navNotifications' => $notifications,
                'navNotificationCount' => (clone $baseQuery)->count(),
                'navNotificationToday' => $hariIni,
            ]);
        });
    }
}
