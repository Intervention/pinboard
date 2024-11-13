<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Bookmark extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'bookmarks';

    /**
     * Protected from mass assignment
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Tags relationship
     *
     * @return HasMany
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Search scope
     *
     * @param Builder $query
     * @param string $keyword
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query
            ->where("title", "like", "%{$keyword}%")
            ->orWhere("url", "like", "%{$keyword}%")
            ->orWhereIn("id", function (QueryBuilder $subquery) use ($keyword) {
                $subquery
                    ->select('bookmark_id')
                    ->from('tags')
                    ->where("title", "like", "{$keyword}%");
            })->orderBy('timestamp');
    }

    /**
     * Return Carbon object with time of last database update
     *
     * @return Carbon
     */
    public static function lastUpdatedAt(): Carbon
    {
        $last = self::orderBy('created_at')->first();

        if (is_null($last)) {
            return Carbon::createFromTimestamp(-1);
        }

        return Carbon::parse($last->created_at);
    }
}
