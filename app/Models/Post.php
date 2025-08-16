<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'published_at' => 'datetime:Y-m-d',
    ];

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'content',
        'published',
        'thumbnail',
        'user_id',
        'published_at',
    ];

    public function setSlugAttribute($value)
    {
        // Generate random timestamp
        $uniqid = uniqid(); // atau bisa juga menggunakan UUID, namun timestamp sudah cukup

        // Format slug dengan timestamp
        $this->attributes['slug'] = "{$uniqid}-{$value}";
    }

    public function setPublishedAttribute($value)
    {
        $this->attributes['published'] = $value === "true" ? true : false;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function liked()
    {
        if (!auth('sanctum')->check()) {
            return false;
        }
        return $this->likes()->where('user_id', auth('sanctum')->user()->id)->exists();
    }

    public function bookmarks(): HasOne
    {
        return $this->hasOne(Bookmark::class);
    }

    public function isBookmark(): bool
    {
        return Bookmark::query()->where('post_id', $this->id)
            ->where('user_id', auth('sanctum')->user()->id)
            ->exists();
    }
}
