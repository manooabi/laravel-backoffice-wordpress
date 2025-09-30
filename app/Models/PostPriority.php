<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPriority extends Model
{
    use HasFactory;
    protected $table = 'post_priorities';

      // Fields that are mass assignable
    protected $fillable = [
        'wp_post_id',
        'priority',
    ];

}
