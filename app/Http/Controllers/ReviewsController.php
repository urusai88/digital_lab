<?php

namespace App\Http\Controllers;

use App\Models\ReviewEntity;
use App\Models\ReviewLikeEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ReviewsController extends Controller
{
    /**
     * @param Request $request
     * @throws Throwable
     */
    public function reviewCreate(Request $request)
    {
        $data = $request->validate([
            'author_name' => 'required|filled|string',
            'body' => 'required|filled|string',
        ]);

        $model = new ReviewEntity();
        $model->author_name = $data['author_name'];
        $model->body = $data['body'];
        $model->ip_address = $request->ip();
        $model->likes_count = 0;

        $model->saveOrFail();

        return $model;
    }

    public function reviewsList(Request $request)
    {
        $query = ReviewEntity::query();

        $sort = $request->query('sort', '');
        if (Str::contains($sort, '-likes'))
            $query->orderBy('likes_count');
        if (Str::contains($sort, '+likes'))
            $query->orderBy('likes_count', 'desc');
        if (Str::contains($sort, '-time'))
            $query->orderBy('created_at');
        if (Str::contains($sort, '+time'))
            $query->orderBy('created_at', 'desc');

        return [
            'items' => (clone $query)
                ->offset($request->query('offset', 0))
                ->limit(10)
                ->get(),
            'count' => $query->count(),
        ];
    }

    public function reviewsLike(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|int|exists:reviews',
        ]);

        return DB::transaction(function () use ($data, $request) {
            /** @var ReviewEntity $review */
            $review = ReviewEntity::query()->findOrFail($data['id']);
            $ipAddress = !config('app.debug') ? $request->ip() : $request->input('ip_address', $request->ip());
            $exists = ReviewLikeEntity::query()
                ->where(['review_id' => $data['id'], 'ip_address' => $ipAddress])
                ->exists();

            if ($exists) {
                return response([
                    'message' => 'Нельзя лайкать одну запись несколько раз',
                ], 422);
            }

            $reviewLike = new ReviewLikeEntity();
            $reviewLike->review_id = $review->id;
            $reviewLike->ip_address = $ipAddress;
            $reviewLike->saveOrFail();

            ReviewEntity::query()->whereKey($review->id)->increment('likes_count');
        });
    }
}
