<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewsTest extends TestCase
{
    use WithFaker;
    use DatabaseMigrations;
    use RefreshDatabase;

    public function testCreateReviews()
    {
        $this->postJson('/api/reviews/create')->assertStatus(422);
        $this->postJson('/api/reviews/create', ['body' => '', 'author_name' => ''])->assertStatus(422);
        $this->postJson('/api/reviews/create', ['body' => 'review body', 'author_name' => ''])->assertStatus(422);
        $this->postJson('/api/reviews/create', ['body' => '', 'author_name' => 'author name'])->assertStatus(422);

        $resp = $this->createReview()->dump()->assertSuccessful()->assertJsonStructure([
            'id', 'body', 'author_name', 'likes_count', 'created_at',
        ]);
        $reviewId = $resp->json('id');

        $this->assertDatabaseHas('reviews', [
            'id' => $reviewId, 'body' => 'review body', 'author_name' => 'author name', 'likes_count' => 0,
        ]);
        $this->assertDatabaseCount('reviews', 1);
    }

    protected function createReview()
    {
        return $this->postJson('/api/reviews/create', [
            'body' => 'review body',
            'author_name' => 'author name',
        ]);
    }
}
