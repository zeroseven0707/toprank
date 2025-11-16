<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrawlData extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'hashtags' => 'array',
        'metadata' => 'array',
    ];

    public function urlCrawl()
    {
        return $this->belongsTo(UrlCrawl::class);
    }
    public function content()
    {
        return $this->hasOne(Content::class);
    }
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'category', 'id');
    }
}
