<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $author_name
 * @property string $body
 * @property int $likes_count
 * @property DateTime $ip_address
 */
class ReviewEntity extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    protected $hidden = ['ip_address'];

    protected static function newFactory()
    {
        return ReviewFactory::new();
    }
}
