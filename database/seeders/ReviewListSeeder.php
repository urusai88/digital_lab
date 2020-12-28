<?php

namespace Database\Seeders;

use App\Models\ReviewEntity;
use Illuminate\Database\Seeder;

class ReviewListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReviewEntity::factory()->count(24)->create();
    }
}
