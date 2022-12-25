<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\BranchRepositoryInterface;
use App\Repositories\BranchRepository;

class BranchRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
