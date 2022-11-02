<?php

namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'rainlab_blog_posts';


    public function featured_images() {
        return $this->belongsTo('App\Models\File','attachment_id','id');
    }

    public function categories() {
        return $this->belongsToMany('App\Models\Category','rainlab_blog_posts_categories', 'post_id', 'category_id');
    }
}
