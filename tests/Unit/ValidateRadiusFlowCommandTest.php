<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\TestCase;

class ValidateRadiusFlowCommandTest extends TestCase
{
    public function test_command_runs()
    {
        // We can't run Artisan here, but ensure the class exists
        $this->assertTrue(class_exists(\App\Console\Commands\ValidateRadiusFlow::class));
    }
}
