<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteBlogPostRequest;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;

use App\Models\BlogPost;

use App\Http\Resources\BlogPostResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Services\BlogPostService;

use Illuminate\Http\JsonResponse;


class BlogPostController extends Controller implements HasMiddleware
{
    // Inject the BlogPostService through the constructor
    public function __construct(
        protected BlogPostService $blogPostService
    ){}

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            // Apply auth::sanctum to everything EXCEPT index and show actions
            new Middleware('auth:sanctum',except:['index','show']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index() : AnonymousResourceCollection
    {
        $blogpost = $this->blogPostService->getAllPosts();

        return BlogPostResource::collection($blogpost);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogPostRequest $request): BlogPostResource
    {
        //
        $blogpost = $this->blogPostService->createPost($request->validated());

        return new BlogPostResource($blogpost);

    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogpost) : BlogPostResource
    {

        return new BlogPostResource($blogpost->loadMissing('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $blogpost) : BlogPostResource
    {

        $updatedpost= $this->blogPostService->updatePost($blogpost,$request->validated());

        return new BlogPostResource($updatedpost);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteBlogPostRequest $request) : JsonResponse
    {
        // Fetch the pre-authorized model directly from the route
        $blogpost = $request->route('blogpost');

        // Execute the service layer operation
        $this->blogPostService->deletePost($blogpost);

        // Return a clean, standardized JSON response back to Postman
        return response()->json(["message" => "Blog Post {$blogpost->id} successfully deleted."],200);

    }
}
