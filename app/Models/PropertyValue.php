<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
    ];

    public function key()
    {
        return $this->belongsTo(PropertyKey::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'key_id');
    }
}
