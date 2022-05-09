<?php

namespace Tests\Feature;

use App\Feed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FeedTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_load()
    {
        $this->assertTrue(Feed::getFeed('test query') != '');
    }
}
