<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;

class CorsServiceProvider extends ServiceProvider
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
        // Add a response macro to handle CORS
        Response::macro('cors', function ($origin = null) {
            $allowedOrigins = [
                'http://localhost:3001',
                'http://127.0.0.1:3001',
                'http://localhost:3000',
                'http://127.0.0.1:3000',
                config('app.frontend_url'),
            ];

            $allowedOrigins = array_filter($allowedOrigins);

            if ($origin && in_array($origin, $allowedOrigins)) {
                $this->header('Access-Control-Allow-Origin', $origin);
                $this->header('Access-Control-Allow-Credentials', 'true');
                $this->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                $this->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN, Accept');
            }

            return $this;
        });
    }
}