<?php

namespace App\Services;

use App\Models\BlogPost;


use App\Interfaces\BlogPostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;


class BlogPostService
{
    // Inject the Repository layer right here
    public function __construct(
        protected BlogPostRepositoryInterface $blogPostRepoInterface
    ){}

    /**
     *  Get all blog posts with their category.
    */
    public function getAllPosts(): Collection
    {
        return $this->blogPostRepoInterface->getAll();
    }
    /**
     * Create a new blog post and load its category.
     */
    public function createPost(array $data): BlogPost
    {
        // pulls id from user
        $data['user_id'] = (int) request()->user()->id;

        // let the repository talk to the db
        $blogpost = $this->blogPostRepoInterface->create($data);

        // business logic/formatting handled by the service layer
        return $blogpost->loadMissing('category');
    }

    /**
     * Update an existing blog post and reload its category.
     */
    public function updatePost(BlogPost $blogpost, array $data): BlogPost
    {
        // Let the repository handle the database update
        $updatedPost = $this->blogPostRepoInterface->update($blogpost,$data);

        // Service layer prepares the final object state
        return $updatedPost->loadMissing('category');
    }
    /**
     * Delete a blog post.
     */
    public function deletePost(BlogPost $blogpost): bool
    {
        return $this->blogPostRepoInterface->delete($blogpost);
    }
}


?>
