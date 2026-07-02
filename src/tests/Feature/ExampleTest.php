<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_home_page_displays_the_minimal_application_shell(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('MyLaravelApp')
            ->assertSee('Hello world');
    }
}
