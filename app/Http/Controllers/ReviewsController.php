<?php

namespace App\Http\Controllers;

use App\Models\ReviewEntity;
use Illuminate\Http\Request;
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

    public function reviewsList()
    {

    }
}
