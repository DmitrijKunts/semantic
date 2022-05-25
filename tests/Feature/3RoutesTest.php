<?php

namespace Tests\Feature;

use App\Models\Cat;
use App\Models\Good;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    public function test_home()
    {
        $this->artisan('cats:import')->assertExitCode(0);
        $this->artisan('keys:import')->assertExitCode(0);

        $c = Cat::first();
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Privacy Policy')
            ->assertSee($c->slug . '.html', false)
            ->assertSee($c->name);
    }

    public function test_cat()
    {
        $c = Cat::active()->inRandomOrder()->first();
        $this->get(route('cat', $c, false))
            ->assertStatus(200)
            ->assertSee($c->name);
    }

    public function test_good()
    {
        $g = Good::first();
        $this->get(route('good', $g, false))
            ->assertStatus(200)
            ->assertSee($g->name)
            ->assertSee($g->summary);
    }

    public function test_robots()
    {
        $this->get('/robots.txt')
            ->assertStatus(200)
            ->assertSee('Allow: /*?page=*')
            ->assertSee('/sitemap.xml');
    }

    public function test_sitemap()
    {
        $c = Cat::first();
        $g = Good::inRandomOrder()->first();
        $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
            ->assertSee(route('cat', $c, false))
            ->assertSee(route('good', $g, false));
    }

    public function test_privacy_policy()
    {
        $this->get('/privacy-policy.html')
            ->assertStatus(200)
            ->assertSee('If you have additional questions or require more information about our Privac');
    }

    public function test_buy()
    {
        $g = Good::inRandomOrder()->first();
        $this->get(route('good', $g, false))
            ->assertStatus(200);
    }

    public function test_img()
    {
        $g = Good::inRandomOrder()->first();
        $this->get(route('img', [$g, 0], false))
            ->assertStatus(200);
    }

    public function test_img_small()
    {
        $g = Good::inRandomOrder()->first();
        $this->get(route('img.small', [$g, 0], false))
            ->assertStatus(200);
    }
}
