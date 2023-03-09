<?php

namespace App\Models;

use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    protected $fillable = [
        'name', 'slug'
    ];
}
