<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'rainlab_blog_posts';


    public function featured_images() {
        return $this->belongsTo(File::class,'attachment_id','id');
    }

    public function categories() {
        return $this->belongsToMany(Category::class,'rainlab_blog_posts_categories', 'post_id', 'category_id');
    }
}
