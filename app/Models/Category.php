<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Users following this category.
     */
    public function followers()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
