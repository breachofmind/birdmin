<?php

namespace Birdmin;

use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model implements Sluggable
{
    use SoftDeletes;

    protected $table = "posts";

    protected $fillable = [
        'title',
        'published_at',
        'excerpt',
        'content',
        'slug',
        'status',
        'type',
        'user_id',
        'location_id'
    ];

    protected $searchable = [
        'title',
        'slug',
        'status',
        'type',
        'excerpt',
        'users.last_name',
        'users.first_name',
        'locations.city',
        'locations.state'
    ];

    protected $appends = ['author'];

    protected $dates = ['created_at','updated_at','published_at','deleted_at'];

    protected $joins = [
        'location_id'   => Location::class,
        'user_id'       => User::class
    ];


    /**
     * Get the post author (query).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the primary author (User) model.
     * @return null|User
     */
    public function getAuthorAttribute()
    {
        return $this->author()->first();
    }

    /**
     * Alias for author, used by data tables.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->author();
    }

    /**
     * Get the post location.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Query scope for published posts.
     * @param $query
     * @return mixed
     */
    public function scopePublished ($query)
    {
        return $query->where('published_at', "<=", Carbon::now())
            ->where('status','publish');
    }

    /**
     * Return a collection of media items, ordered by priority.
     * @return Collection
     */
    public function media()
    {
        return Relationship::collection($this, Media::class);
    }

    /**
     * Return a URL for this model on the frontend.
     * @return string
     */
    public function url($relative=false)
    {
        return $relative ? "/".$this->slug : url($this->slug);
    }
}
