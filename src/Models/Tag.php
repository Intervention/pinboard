<?php

declare(strict_types=1);

namespace Intervention\Pinboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * @return BelongsTo
     */
    public function bookmark(): BelongsTo
    {
        return $this->belongsTo(Bookmark::class);
    }
}
