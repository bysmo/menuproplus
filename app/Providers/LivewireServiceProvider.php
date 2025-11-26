<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Vérifier que Livewire est installé
        if (!class_exists(Livewire::class)) {
            return;
        }

        // ✅ Enregistrer la route UPDATE de Livewire
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)
                ->middleware(['web'])
                ->name('livewire.update');
        });

        // ✅ Enregistrer la route JavaScript de Livewire
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/livewire/livewire.js', $handle)
                ->name('livewire.javascript');
        });

        // ✅ Si vous utilisez le téléchargement de fichiers
        if (method_exists(Livewire::class, 'setUploadRoute')) {
            Livewire::setUploadRoute(function ($handle) {
                return Route::post('/livewire/upload-file', $handle)
                    ->middleware(['web'])
                    ->name('livewire.upload-file');
            });

            Livewire::setPreviewRoute(function ($handle) {
                return Route::get('/livewire/preview-file/{filename}', $handle)
                    ->middleware(['web'])
                    ->name('livewire.preview-file');
            });
        }
    }
}
