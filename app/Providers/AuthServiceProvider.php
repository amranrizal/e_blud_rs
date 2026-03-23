<?php

namespace App\Providers;

use App\Models\Anggaran;
use App\Policies\AnggaranPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        PaguIndikatif::class => PaguIndikatifPolicy::class,    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
