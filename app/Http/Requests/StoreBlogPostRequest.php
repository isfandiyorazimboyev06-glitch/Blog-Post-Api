<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'author' => 'required|string|max:255',
            'post' => 'required|string|max:255',
            'category_blog_post_id' => 'required|exists:category_blog_posts,id'
        ];
    }

    public function message()
    {
        return [
            'author.string' => 'The author field must be string type only.',
            'post.string' => 'The post field must be string type only.',
            'category_blog_post_id.exists' => 'Given category blog must be in db.'
        ];
    }
}
