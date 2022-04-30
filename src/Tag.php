<?php

namespace Intervention\Pinboard;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'bookmark_id',
    ];

    /**
     * Bookmark object
     *
     * @return ?Bookmark
     */
    public function bookmark()
    {
        return $this->belongsTo(Bookmark::class);
    }
}
