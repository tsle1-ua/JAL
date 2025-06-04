<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'sender_id',
        'content',
    ];

    public function match()
    {

    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
