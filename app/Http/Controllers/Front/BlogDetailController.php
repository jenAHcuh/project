<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\post;

class BlogDetailController extends Controller
{
    public function detail($slug)
    {
        // Fetch the current blog post
        $data = post::where('status', 'publish')->where('slug', $slug)->firstOrFail();

        // Get pagination data
        $pagination = $this->pagination($data->id);

        // Pass the data to the view
        return view('components.front.blog-detail', compact('data', 'pagination'));
    }

    private function pagination($currentPostId)
    {
        // Fetch the previous post
        $dataPrev = post::where('status', 'publish')
                        ->where('id', '<', $currentPostId)
                        ->orderBy('id', 'desc')
                        ->first();

        // Fetch the next post
        $dataNext = post::where('status', 'publish')
                        ->where('id', '>', $currentPostId)
                        ->orderBy('id')
                        ->first();

        // Prepare pagination data
        return [
            'prev' => $dataPrev,
            'next' => $dataNext
        ];
    }
}