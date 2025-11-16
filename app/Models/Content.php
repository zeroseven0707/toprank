<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Content extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    /**
     * Relasi ke kategori konten
     */
    public function crawlData()
    {
        return $this->belongsTo(CrawlData::class);
    }

    public function category()
    {
        return $this->belongsTo(ContentCategory::class, 'category_content_id');
    }
        public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
}
