<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBlogPost extends Model
{
    //
    protected $fillable = ['category_name'];

    public function blogpost()
    {
        return $this->hasMany(BlogPost::class);
    }
}
