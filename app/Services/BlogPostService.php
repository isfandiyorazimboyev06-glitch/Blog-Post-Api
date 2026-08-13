<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Collection;

class BlogPostService
{
    /**
     *  Get all blog posts with their category.
    */
    public function getAllPosts(): Collection
    {
        return BlogPost::with('category')->get();
    }
    /**
     * Create a new blog post and load its category.
     */
    public function createPost(array $data): BlogPost
    {
        $blogpost = BlogPost::create($data);

        return $blogpost->loadMissing('category');
    }

    /**
     * Update an existing blog post and reload its category.
     */
    public function updatePost(BlogPost $blogpost, array $data): BlogPost
    {
        $blogpost->update($data);

        return $blogpost->loadMissing('category');
    }
    /**
     * Delete a blog post.
     */
    public function deletePost(BlogPost $blogpost): bool
    {
        return $blogpost->delete();
    }
}


?>
