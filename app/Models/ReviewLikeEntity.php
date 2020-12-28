<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $review_id
 * @property string $ip_address
 */
class ReviewLikeEntity extends Model
{
    use HasFactory;

    protected $table = 'review_likes';
}
