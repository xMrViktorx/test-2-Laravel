<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
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
        // If a user doesn't have permissions to import any import types, Data Imports tab should not be visible.
        Gate::define('data-import', function ($user) {
            $importTypes = config('import');
    
            foreach ($importTypes as $type) {
                if ($user->can($type['permission_required'])) {
                    return true;
                }
            }
    
            return false;
        });

        $importConfig = config('import'); // Load the import configuration

        // Fetch the AdminLTE menu
        $menu = Config::get('adminlte.menu');

        // Add "Imported Data" submenu dynamically
        foreach ($menu as &$item) {
            if (isset($item['text']) && $item['text'] != 'search') {
                if ($item['text'] === 'Imported Data') {
                    // Check if the submenu already contains the items
                    $existingLabels = array_column($item['submenu'] ?? [], 'text');
                    foreach ($importConfig as $key => $dataset) {
                        foreach($dataset['files'] as $fileType => $file) {
                            if (!in_array($file['label'], $existingLabels)) {
                                $item['submenu'][] = [
                                    'text' => $file['label'],
                                    'url' => '/imported-data/' . $fileType,
                                    'icon' => 'fas fa-fw fa-table',
                                ];
                            }
                        }
                    }
                    break;
                }
            }
        }

        Config::set('adminlte.menu', $menu); // Save the updated menu
    }
}
