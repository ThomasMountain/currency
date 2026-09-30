<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The Vite manifest lives in public/build, which is gitignored, so a
        // fresh clone has no compiled assets. Stub the tags out so feature
        // tests can render views without running `npm run build` first.
        $this->withoutVite();
    }
}
