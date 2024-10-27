<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Http\Requests\StoreLikeRequest;
use App\Http\Requests\UpdateLikeRequest;
use Exception;
use App\Models\Blog;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($blog_id)
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                response()->json(['message' => 'Not autheticated'], 401);
            }

            $blog = Blog::find($blog_id);
            if (!$blog) {
                return response()->json(['message' => 'Blog not found'], 404);
            }

            $likeCount = Like::where('blog_id', $blog_id)->count();

            $liked = false;

            if ($user) {
                $liked = Like::where('blog_id', $blog_id)
                    ->where('author_id', $user->id)
                    ->exists();
            }

            return response()->json([
                'message' => 'Fetched Successfully!',
                'likeCount' => $likeCount,
                'liked' => $liked
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLikeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLikeRequest $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Like $like)
    {
        //
    }

    public function toogleLike($blog_id)
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                response()->json(['message' => 'Not autheticated'], 401);
            }

            $blog = Blog::find($blog_id);

            if (!$blog) {
                return response()->json(['message' => 'Blog not found'], 404);
            }

            $like = Like::where('blog_id', $blog_id)->where('author_id', $user->id)->first();

            if ($like) {
                $like->delete();
                $likeCount = Like::where('blog_id', $blog_id)->count();
                return response()->json(['message' => 'Post unliked successfully', 'likeCount' => $likeCount, 'liked' => false], 200);
            } else {
                Like::create([
                    'blog_id' => $blog_id,
                    'author_id' => $user->id,
                ]);

                $likeCount = Like::where('blog_id', $blog_id)->count();
                return response()->json(['message' => 'Post liked successfully', 'likeCount' => $likeCount, 'liked' => true], 201);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
