<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlCrawl extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function crawlData()
    {
        return $this->hasMany(CrawlData::class);
    }
    /**
     * Get the city that owns the UrlCrawl
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'category', 'id');
    }
}
