<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Tests\TestCase;

class HelperTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_genConst()
    {
        $this->assertTrue(genConst(1000, '123') == genConst(1000, '123'));
        $this->assertNotTrue(genConst(1000, '123') == genConst(1000, '1234'));
    }

    public function test_constSort()
    {
        $this->assertTrue(constSort(range(0, 1000), '123') == constSort(range(0, 1000), '123'));
        $this->assertNotTrue(constSort(range(0, 1000), '123') == constSort(range(0, 1000), '1234'));
    }

    public function test_constOne()
    {
        $this->assertTrue(constOne(range(0, 1000), '123') == constOne(range(0, 1000), '123'));
        $this->assertNotTrue(constOne(range(0, 1000), '123') == constOne(range(0, 1000), '1234'));
    }

}
