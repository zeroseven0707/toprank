<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    protected $fillable = ['content_category_id', 'order', 'status'];

    /**
     * Get the contentCategory that owns the Section
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentCategory(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'content_category_id', 'id');
    }
}
