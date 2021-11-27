<?php

namespace TechTailor\WazirxApi;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WazirxApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('wazirx-api')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        //
    }
}
