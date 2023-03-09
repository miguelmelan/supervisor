<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
    ];

    public function values()
    {
        return $this->hasMany(PropertyValue::class, 'key_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'key_id');
    }
}
