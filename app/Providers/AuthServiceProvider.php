<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\Usuario;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        // Esto asegura que Sanctum use el modelo Usuario
        Sanctum::usePersonalAccessTokenModel(\Laravel\Sanctum\PersonalAccessToken::class);

        Sanctum::authenticateAccessTokensUsing(function ($request) {
            return Usuario::find(optional($request->user())->id);
        });
    }
}
