<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

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
        try {
            // Đăng ký Observer trong môi trường phù hợp
            if (!app()->runningUnitTests()) {
                $this->registerObservers();
            }
        } catch (\Exception $e) {
            Log::error('Error registering observers: ' . $e->getMessage());
        }
    }

    /**
     * Register model observers
     */
    private function registerObservers(): void
    {
        try {
            //  ModelClass::observe(MediaModelObserver::class);

            $observed = [
                \App\Models\MediaFile::class     => \App\Observers\MediaFileObserver::class,
                \App\Models\MediaFolder::class   => \App\Observers\MediaFolderObserver::class,
                \App\Models\User::class          => \App\Observers\UserObserver::class,
            ];

            foreach ($observed as $model => $observer) {
                if (class_exists($model) && class_exists($observer)) {
                    $model::observe($observer);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in registerObservers: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check if the current request is running a seeder or migration
     */
    protected function isRunningSeeder(): bool
    {
        $argv = $_SERVER['argv'] ?? [];

        return collect($argv)->contains(function ($arg) {
            return str_contains($arg, 'db:seed') ||
                str_contains($arg, 'migrate') ||
                str_contains($arg, 'db:refresh') ||
                str_contains($arg, 'db:wipe');
        });
    }
}
