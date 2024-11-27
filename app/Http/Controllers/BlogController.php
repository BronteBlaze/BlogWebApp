<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use App\Notifications\BlogSubmittedNotification;
use App\Notifications\BlogStatusNotification;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Blog::where('status', 'approved')->orderBy('created_at', 'desc')->paginate(8);
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
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'category' => 'required|string',
                'title' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        if (str_word_count($value) > 3) {
                            $fail('The ' . $attribute . ' must not exceed 3 words.');
                        }
                    },
                ],
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'required|string',
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $validatedData['image'] = $imagePath;
            }


            $validatedData['author_id'] = auth('sanctum')->user()->id;

            $blog = Blog::create($validatedData);
            $blog->image = $request->hasFile('image') ? asset('storage/' . $imagePath) : null;

            $admin = User::where('role', 'superadmin')->first();
            $admin->notify(new BlogSubmittedNotification($blog, $validatedData['author_id']));

            return response()->json([
                'message' => 'Blog submitted successfully!',
                'blog' => $blog
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $blog = Blog::find($id);

            if (!$blog) {
                return response()->json(['message' => 'Blog Not Found'], 404);
            }

            return response()->json(['blog' => $blog], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {

            $validatedData = $request->validate([
                'category' => 'required|string',
                'title' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        if (str_word_count($value) > 3) {
                            $fail('The ' . $attribute . ' must not exceed 3 words.');
                        }
                    },
                ],
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'description' => 'required|string',
            ]);


            $blog = Blog::find($id);

            if (!$blog) {
                return response()->json(['message' => 'Blog not found'], 404);
            }


            $user = auth('sanctum')->user();

            if ($user->id !== $blog->author_id && $user->role !== 'superadmin') {
                return response()->json(['message' => 'Unauthorized to update the blog'], 403);
            }

            $blog->title = $validatedData['title'];
            $blog->category = $validatedData['category'];
            $blog->description = $validatedData['description'];


            if ($request->hasFile('image')) {

                $imageToBeDelete = public_path(str_replace('/', DIRECTORY_SEPARATOR, 'storage/') . str_replace('/', DIRECTORY_SEPARATOR, $blog->image));

                Log::info($imageToBeDelete);

                if (File::exists($imageToBeDelete)) {
                    File::delete($imageToBeDelete);
                }

                $imagePath = $request->file('image')->store('images', 'public');

                $blog->image = $imagePath;
            }

            $blog->save();

            return response()->json(['message' => 'Blog updated successfully', 'blog' => $blog], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $blog = Blog::find($id);

            if (!$blog) {
                return response()->json(['message' => 'Blog not found'], 404);
            }

            $user = auth('sanctum')->user();

            if ($user->id !== $blog->author_id && $user->role !== 'superadmin') {
                return response()->json(['message' => 'Unauthorized to delete the blog'], 403);
            }

            $imagePath = public_path(str_replace('/', DIRECTORY_SEPARATOR, 'storage/') . str_replace('/', DIRECTORY_SEPARATOR, $blog->image));

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            $blog->delete();

            return response()->json(['message' => 'Blog Deleted Successfully', 'blogId' => $blog->id], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function getBlogsByCategory($category): JsonResponse
    {
        try {
            $blogs = Blog::where('category', $category)
                ->orderBy('created_at', 'desc')->paginate(8);

            return response()->json(['blogs' => $blogs], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getBlogsByMonth($month): JsonResponse
    {
        try {
            if ($month < 1 || $month > 12) {
                return response()->json(['message' => 'Invalid month provided'], 400);
            }

            $blogs = Blog::whereMonth('created_at', $month)->orderBy('created_at', 'desc')
                ->paginate(8);

            if (!$blogs) {
                return response()->json(['message' => 'No blogs found for this month'], 404);
            }

            return response()->json(['message' => 'Fetched Successfully!', 'blogs' => $blogs], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function uploadProfilePicture(Request $request): JsonResponse
    {
        try {
            $id = auth('sanctum')->user()->id;

            $user = User::findOrFail($id);

            $request->validate([
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            ]);

            if ($request->hasFile('profile_pic')) {
                $filePath = $request->file('profile_pic')->store('profile_pics', 'public');
                $user->profile_pic = $filePath;
            }

            $user->save();

            return response()->json(['profile_pic' => $user->profile_pic], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage() . 'Try using jpeg,png,jpg,gif,svg image',], 422);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function yourBlogs(): JsonResponse
    {
        try {
            $userId = auth('sanctum')->user()->id;

            $blogs = Blog::where('author_id', $userId)->paginate(4);

            if (!$blogs) {
                return  response()->json(['message' => 'You have no blogs'], 404);
            }

            return response()->json(['blogs' => $blogs]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function share($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return response()->view('share', compact('blog'))->header('Content-Type', 'text/html');
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function incrementView($blog_id)
    {
        try {
            $blog = Blog::find($blog_id);
            if (!$blog) {
                return response()->json(['message' => 'Blog not found'], 404);
            }
            $blog->increment('views');
            return response()->json(['message' => 'View incremented successfully', 'views' => $blog->views]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getNotifications() {
        try {
            $user = auth('sanctum')->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $full_name = "";

            $notifications = $user->notifications()->where('notifiable_id', $user->id)->get();
            $notificationsWithUsernames = $notifications->map(function ($notification) {      
                $userId = $notification->data['user_id'] ?? null;
        
                $userWhoPost = User::find($userId);

                $full_name = $userWhoPost->first_name." ".$userWhoPost->last_name;
                
                $data = $notification->data;
                $data['username'] = $full_name;
                $notification->data = $data;

                return $notification;
            });
            // Fetch all notifications for the user
    
            return response()->json(['notifications' => $notificationsWithUsernames]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    

    public function updateStatus(Request $request, $id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->status = $request->status; // 'approved' or 'rejected'
            $blog->save();
    
            // Notify User
            $user = User::findOrFail($blog->author_id);
    
            // Send notification
            $user->notify(new BlogStatusNotification($blog, $blog->status));
    
            return response()->json(['message' => 'Blog status updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
