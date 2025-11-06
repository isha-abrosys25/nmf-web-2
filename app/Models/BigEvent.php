<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BigEvent extends Model
{
    use HasFactory;

    // Table name (optional if following Laravel conventions)
    protected $table = 'big_events';

    // Mass assignable attributes
    protected $fillable = [
        'title',
        'eng_name',
        'site_url',
        'author_id',
        'category_id',
        'short_desc',
        'description',
        'event_image',
        'video_path',
        'video_thumb',
        'background_image',
        'banner_image',
        'is_active',
        'tag',
    ];

    /**
     * Relationships
     */

    // Author of the big event
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Category of the big event
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Blogs attached to this big event (many-to-many via pivot table)
    public function blogs()
    {
        return $this->belongsToMany(
            Blog::class,
            'big_event_blogs',
            'big_event_id',
            'blog_id'
        )->withTimestamps()
         ->withPivot('id', 'sort_order');
    }

    public function comments()
    {
        return $this->morphMany(ViewerComment::class, 'commentable')
            ->whereNull('parent_id')
            ->with(['replies.viewer', 'viewer'])
            ->latest();
    }

    public function allComments()
    {
        return $this->morphMany(ViewerComment::class, 'commentable');
    }

    public function commentsCount()
    {
        return $this->allComments()->count();
    }
}
