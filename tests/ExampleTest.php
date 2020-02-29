<?php

namespace Fc9\Net\Tests;

use Orchestra\Testbench\TestCase;
use Fc9\Net\Providers\NetServiceProvider;

class ExampleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [NetServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
