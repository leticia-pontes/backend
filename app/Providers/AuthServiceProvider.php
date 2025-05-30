<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Avaliacao;
use App\Policies\AvaliacaoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Avaliacao::class => AvaliacaoPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
