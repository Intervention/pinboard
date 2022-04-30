<?php

namespace Intervention\Pinboard;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use GuzzleHttp\Client;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Search scope
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string                              $keyword
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSearch($query, $keyword)
    {
        return $query
                ->where("title", "like", "%{$keyword}%")
                ->orWhere("url", "like", "%{$keyword}%")
                ->orWhereIn("id", function ($subquery) use ($keyword) {
                    $subquery
                        ->select('bookmark_id')
                        ->from('tags')
                        ->where("title", "like", "{$keyword}%");
                })->orderBy('timestamp');
    }

    /**
     * Return HTTP status code of bookmark url
     *
     * @return int
     */
    public function getHttpStatusAttribute()
    {
        $client = new Client();

        try {
            $response = $client->request('GET', $this->url, [
                'http_errors' => false
            ]);
        } catch (Exception $e) {
            return 200;
        }

        return $response->getStatusCode();
    }

    /**
     * Return Carbon object with time of last database update
     *
     * @return \Carbon\Carbon
     */
    public static function lastUpdatedAt()
    {
        $last = self::orderBy('created_at')->first();

        if (is_null($last)) {
            return Carbon::createFromTimestamp(-1);
        }

        return Carbon::parse($last->created_at);
    }
}
