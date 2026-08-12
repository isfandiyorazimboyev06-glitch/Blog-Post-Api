<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;

use App\Models\BlogPost;
use App\Http\Resources\BlogPostResource;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogpost = BlogPost::with('category')->get();

        return BlogPostResource::collection($blogpost);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogPostRequest $request)
    {
        //
        $blogpost = BlogPost::create($request->validated());

        $blogpost->load('category');

        return new BlogPostResource($blogpost);

    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogpost)
    {
        //
        $blogpost->load('category');

        return new BlogPostResource($blogpost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $blogpost)
    {
        //
        $blogpost->update($request->validated());

        $blogpost->load('category');

        return new BlogPostResource($blogpost);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogpost)
    {
        //
        $blogpost->delete();

    return response()->json(["message" => "Blog Post {$blogpost->id} successfully deleted."],200);

    }
}
