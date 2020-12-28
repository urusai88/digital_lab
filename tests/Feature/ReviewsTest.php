<?php

namespace Tests\Feature;

use App\Models\ReviewLikeEntity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewsTest extends TestCase
{
    use WithFaker;
    use DatabaseMigrations;
    use RefreshDatabase;

    protected function createReview($authorName = 'author name', $reviewBody = 'review body')
    {
        return $this->postJson('/api/reviews/create', [
            'body' => $reviewBody,
            'author_name' => $authorName,
        ]);
    }

    public function testCreateReviews()
    {
        $this->postJson('/api/reviews/create')->assertStatus(422);
        $this->postJson('/api/reviews/create', ['body' => '', 'author_name' => ''])->assertStatus(422);
        $this->postJson('/api/reviews/create', ['body' => 'review body', 'author_name' => ''])->assertStatus(422);
        $this->postJson('/api/reviews/create', ['body' => '', 'author_name' => 'author name'])->assertStatus(422);

        $resp = $this->createReview()->assertSuccessful()->assertJsonStructure([
            'id', 'body', 'author_name', 'likes_count', 'created_at',
        ]);
        $reviewId = $resp->json('id');

        $this->assertDatabaseHas('reviews', [
            'id' => $reviewId, 'body' => 'review body', 'author_name' => 'author name', 'likes_count' => 0,
        ]);
        $this->assertDatabaseCount('reviews', 1);
    }

    public function testLikeReviews()
    {
        $resp = $this->createReview();
        $reviewId = $resp->json('id');

        $this->postJson('/api/reviews/like')->assertStatus(422); // no data
        $this->postJson('/api/reviews/like', ['id' => 0])->assertStatus(422); // wrong data
        $this->postJson('/api/reviews/like', ['id' => $reviewId])->assertSuccessful(); // success
        $this->postJson('/api/reviews/like', [
            'id' => $reviewId, 'ip_address' => $this->faker->ipv4,
        ])->assertSuccessful(); // другой адрес
        $this->postJson('/api/reviews/like', ['id' => $reviewId])->assertStatus(422); // повторный лайк

        $this->assertDatabaseHas('reviews', ['likes_count' => 2]); // проверка счётчика
        $this->assertEquals(2, ReviewLikeEntity::query()->where(['review_id' => $reviewId])->count()); // проверка количества строк
    }

    public function testListReviews()
    {
        $attributes = [];
        for ($i = 0; $i < 24; ++$i) {
            $attributes[] = [$this->faker->name, $this->faker->text];
        }
        $collection = collect($attributes);

        foreach ($attributes as $attributeList) {
            $this->createReview($attributeList[0], $attributeList[1]);
        }

        // стандартый список
        $resp1 = $this->getJson('/api/reviews/list')->assertSuccessful()->assertJsonCount(10);
        $resp2 = $this->getJson('/api/reviews/list?offset=10')->assertSuccessful()->assertJsonCount(10);
        $resp3 = $this->getJson('/api/reviews/list?offset=20')->assertSuccessful()->assertJsonCount(4);

        $this->assertEquals($collection->last()[0], data_get($resp1->json(), '0.author_name'));
        $this->assertEquals($collection->first()[0], data_get($resp3->json(), '3.author_name'));


    }
}
