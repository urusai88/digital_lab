<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Reviews extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('author_name');
            $blueprint->string('ip_address');
            $blueprint->text('body');
            $blueprint->integer('likes_count')->default(0);
            $blueprint->timestamps();
        });

        Schema::create('review_likes', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->integer('review_id');
            $blueprint->string('ip_address');
            $blueprint->timestamps();

            $blueprint->unique(['review_id', 'ip_address']);
            $blueprint->foreign('review_id')->references('id')->on('reviews')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::drop('review_likes');
        Schema::drop('reviews');
    }
}
