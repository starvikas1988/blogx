<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
     protected $fillable = ['post_id','user_id','body'];

    public function post() { return $this->belongsTo(Post::class); }
    public function author() { return $this->belongsTo(User::class, 'user_id'); }
}
