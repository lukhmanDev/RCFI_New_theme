<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['title', 'message', 'url'];

    protected static function booted()
    {
        static::created(function ($notification) {
            $users = User::all();
            foreach ($users as $user) {
                NotificationRecipient::create([
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'is_read' => false,
                ]);
            }
        });
    }

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }
}
