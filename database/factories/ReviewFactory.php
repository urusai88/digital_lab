<?php

namespace Database\Factories;

use App\Models\ReviewEntity;
use App\Models\ReviewLikeEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReviewEntity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'author_name' => $this->faker->name,
            'ip_address' => $this->faker->ipv4,
            'body' => $this->faker->text(500),
            'likes_count' => $this->faker->numberBetween(0, 10),
            'created_at' => $dt = $this->faker->dateTime,
            'updated_at' => $dt,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (ReviewEntity $review) {
            for ($i = 0; $i < $review->likes_count; ++$i) {
                $faker = $this->faker->unique();
                $like = new ReviewLikeEntity();
                $like->ip_address = $faker->ipv4;
                $like->review_id = $review->id;
                $like->save();
            }
        });
    }
}
