<?php

namespace Tests\Feature;

use App\Models\Cat;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ImportTest extends TestCase
{
    // use DatabaseMigrations;
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_with_keys()
    {
        $this->artisan('cats:import')->assertExitCode(0);
        $this->artisan('keys:import')->assertExitCode(0);

        $this->assertDatabaseHas('cats', [
            'name' => 'Зонт Xiaomi 90 points all purpose umbrella',
        ]);
        $this->assertDatabaseMissing('cats', [
            'name' => 'YuihY&Iyui^78tytgjhfgf',
        ]);

        $this->assertDatabaseMissing('keys', [
            'name' => 'зонт xiaomi automatic umbrella',
        ]);
        $this->assertDatabaseHas('keys', [
            'name' => 'зонт мужской xiaomi купить',
        ]);

        $this->artisan('keys:snippet')->assertExitCode(0);
        $this->assertDatabaseHas('snippets', [
            'snippet' => 'Xiaomi Mi Mijia Automatic Umbrella - зонт, который прослужит вам не один год. Спицы и ручка зонта выполнены из невероятно легких и высококачественных материалов ...',
        ]);
        $this->assertDatabaseMissing('snippets', [
            'snippet' => 'YuihY&Iyui^78tytgjhfgf',
        ]);

        $this->assertDatabaseHas('youtubes', [
            'url' => 'https://www.youtube.com/watch?v=rVHZ0r0yKZs',
        ]);
        $this->assertDatabaseMissing('youtubes', [
            'url' => 'YuihY&Iyui^78tytgjhfgf',
        ]);

        $this->assertDatabaseHas('youtubes', [
            'title' => 'Автоматический Зонт Xiaomi со светоотражающей полосой и фонариком в ручке на 10 спиц (1т.р.)',
        ]);
        $this->assertDatabaseMissing('youtubes', [
            'title' => 'YuihY&Iyui^78tytgjhfgf',
        ]);

        $this->assertDatabaseHas('youtubes', [
            'snippet' => 'Автоматический Зонт Xiaomi со светоотражающей полосой, зонтик со светодиодной подсветкой, складной, перевернутый, ...',
        ]);
        $this->assertDatabaseMissing('youtubes', [
            'snippet' => 'YuihY&Iyui^78tytgjhfgf',
        ]);
    }

    public function test_without_keys()
    {
        $this->artisan('cats:import', ['--without_keys' => true])->assertExitCode(0);


        $this->assertDatabaseHas('keys', [
            'name' => 'зонт xiaomi купить',
        ]);
        $this->assertDatabaseMissing('keys', [
            'name' => 'зонт xiaomi automatic umbrellaвцыкацукауцкцука',
        ]);
    }

    public function test_text()
    {
        $this->artisan('cats:import', ['--without_keys' => true])->assertExitCode(0);

        $this->assertTrue(Cat::where('text', '<>', '')->count() == 5);
        $this->assertTrue(Cat::where('text', 'like', '%Если же вы любите яркие цвета, то выбирайте%')->count() == 1);
    }
}
