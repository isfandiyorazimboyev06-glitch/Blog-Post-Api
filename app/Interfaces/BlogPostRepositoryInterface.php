<?php

namespace App\Interfaces;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Collection;

interface BlogPostRepositoryInterface
{
    public function getAll(): Collection;

    public function create(array $data): BlogPost;

    public function update(BlogPost $blogpost, array $data): BlogPost;

    public function delete(BlogPost $blogpost): bool;

}
?>
