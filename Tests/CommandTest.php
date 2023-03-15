<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class CommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_fires_require_pin_commands(): void
    {
        $this->artisan('sas:controllers')->assertSuccessful();

        $this->artisan('vendor:publish --tag=sas-config')->assertSuccessful();

        $this->artisan('vendor:publish --tag=sas-migrations')->assertSuccessful();

        $this->artisan('vendor:publish --tag=sas-lang')->assertSuccessful();

        $this->artisan('vendor:publish --tag=sas-views')->assertSuccessful();

        $this->artisan('vendor:publish --tag=sas-feature-tests')->assertSuccessful();
    }
}
