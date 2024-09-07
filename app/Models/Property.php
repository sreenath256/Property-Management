<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_name',
        'phone',
        'email',
        'price',
        'currency',
        'description',
        'inclusions_exclusions',
        'primary_image',
        'property_slug',
        'user_id'
    ];

    public function facilities()
    {
        return $this->hasMany(PropertyFacility::class);
    }
}
