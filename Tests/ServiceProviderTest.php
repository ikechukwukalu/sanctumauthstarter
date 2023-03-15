<?php

namespace Ikechukwukalu\Sanctumauthstarter\Tests;

use Ikechukwukalu\Sanctumauthstarter\SanctumauthstarterServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function test_merges_config(): void
    {
        static::assertSame(
            $this->app->make('files')
                ->getRequire(SanctumauthstarterServiceProvider::CONFIG),
            $this->app->make('config')->get('sanctumauthstarter')
        );
    }

    public function test_loads_translations(): void
    {
        static::assertArrayHasKey('sanctumauthstarter',
            $this->app->make('translator')->getLoader()->namespaces());
    }

}
