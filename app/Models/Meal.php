<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'image',
    ];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot('notes', 'serving_quantity')
            ->withTimestamps();
    }
}
