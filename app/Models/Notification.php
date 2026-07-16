<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
<<<<<<< HEAD
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
=======
    protected $fillable = ['title', 'message', 'url', 'is_read'];
>>>>>>> 931b70b15894ca6c070c71c54872cb207eaf9da3
}
