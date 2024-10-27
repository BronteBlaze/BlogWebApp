<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

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
    public function store(Request $request, $blog_id)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|max:500',
            ]);

            $comment = Comment::create([
                'blog_id' => (int) $blog_id,
                'author_id' => auth('sanctum')->user()->id,
                'content' => $validated['content'],
            ]);

            $comment->load('user');

            return response()->json([
                'comment' => [
                    'author_id' => $comment->author_id,
                    'blog_id' => $comment->blog_id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'id' => $comment->id,
                    'updated_at' => $comment->updated_at,
                    'user' => $comment->user
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Failed'], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($blog_id)
    {
        try {
            $comments = Comment::where('blog_id', $blog_id)->with('user')->get();
            return response()->json(['comments' => $comments], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if (auth('sanctum')->user()->id !== $comment->author_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $comment->delete();

            return response()->json(['message' => 'Comment deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Comment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
