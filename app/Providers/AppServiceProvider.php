<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        Auth::provider('legacy', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends \Illuminate\Auth\EloquentUserProvider {
                public function retrieveById($identifier)
                {
                    return User::find($identifier);
                }

                public function retrieveByCredentials(array $credentials)
                {
                    if (empty($credentials) || (count($credentials) === 1 && array_key_exists('password', $credentials))) {
                        return;
                    }

                    return User::findByCredentials($credentials);
                }

                public function validateCredentials(\Illuminate\Contracts\Auth\Authenticatable $user, array $credentials)
                {
                    $plain = $credentials['password'];
                    return trim($plain) === trim($user->getAuthPassword());
                }
            };
        });
    }
}
