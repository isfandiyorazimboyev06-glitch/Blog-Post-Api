<?php

namespace App\Repositories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Collection;

use App\Interfaces\BlogPostRepositoryInterface;

class BlogPostRepository implements BlogPostRepositoryInterface
{
    /**
     * Fetch all blog posts eager loading the category.
     */
    public function getAll(): Collection
    {
        return BlogPost::with('category')->get();
    }

    /**
     * Create a new blog post.
     */
    public function create(array $data): BlogPost
    {

        return BlogPost::create($data);
    }
    /**
     * Update an existing blog post record.
     */
    public function update(BlogPost $blogpost, array $data): BlogPost
    {
        $blogpost->update($data);
        return $blogpost;
    }

    /**
     * Delete a blog post record from the database.
     */
    public function delete(BlogPost $blogpost): bool
    {
        return $blogpost->delete();
    }
}
?>
