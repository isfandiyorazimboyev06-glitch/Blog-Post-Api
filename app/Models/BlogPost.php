<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    //
    protected $fillable = ['author','post','category_blog_post_id','user_id'];

    public function category()
    {
        return $this->belongsTo(CategoryBlogPost::class, 'category_blog_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
