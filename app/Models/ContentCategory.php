<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    protected $guarded = ['id'];

    public function contents()
    {
        return $this->hasMany(Content::class, 'category_content_id');
    }
}
