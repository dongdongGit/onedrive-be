<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Image;
use App\Models\OneDrive;
use App\Models\Task;
use App\Observers\AdminObserver;
use App\Observers\ImageObserver;
use App\Observers\OneDriveObserver;
use App\Observers\TaskObserver;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Carbon::setLocale('zh');
        CarbonInterval::setLocale('zh');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::serializeUsing(function ($carbon) {
            return $carbon->format('Y-m-d H:i:s');
        });
        Task::observe(TaskObserver::class);
        Admin::observe(AdminObserver::class);
        Image::observe(ImageObserver::class);
        OneDrive::observe(OneDriveObserver::class);
    }
}
