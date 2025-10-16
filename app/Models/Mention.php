<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    protected $fillable = [
        'feedback_id',
        'mentioned_user_id',
        'mentioner_user_id',
        'username_mentioned',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }

    public function mentionerUser()
    {
        return $this->belongsTo(User::class, 'mentioner_user_id');
    }
}
