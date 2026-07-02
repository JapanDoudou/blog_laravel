<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_displays_the_article_listing_shell(): void
    {
        Article::factory()->published()->create([
            'title' => 'Hello world',
            'content' => '<p>Base Laravel MVC minimale avec Bootstrap et Docker.</p>',
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('MyLaravelApp')
            ->assertSee('Articles')
            ->assertSee('Hello world');
    }
}
