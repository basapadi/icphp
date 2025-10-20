<?php

namespace App\Providers;

use Native\Desktop\Facades\{Window,Settings};
use Native\Desktop\Contracts\ProvidesPhpIni;
use Illuminate\Support\Facades\Artisan;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        // Menu::create();
        // MenuBar::create()->label('Status: Online');
        Window::open()
            ->minWidth(800)
            ->minHeight(500)
            ->width(1200)
            ->height(800)
            ->title('Ihand Cashier')
            ->hasShadow(true)
            ->rememberState()->hideMenu();

        $firstRun = Settings::get('first_run', false);

        if (!$firstRun) {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('route:cache');

            Settings::set('first_run', true);
        } else {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('route:cache');
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '1048M',
            'display_errors' => '1',
            'error_reporting' => 'E_ALL',
            'max_execution_time' => '0',
            'max_input_time' => '0',
        ];
    }
}
