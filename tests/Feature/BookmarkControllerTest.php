<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookmarkControllerTest extends TestCase
{
     use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function it_can_store_a_bookmark_successfully(): void
    {
        $user = User::factory()->
    }
}
