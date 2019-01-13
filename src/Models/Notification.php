<?php

namespace DigitalCloud\AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $table = 'notification_notifications';

    protected $fillable = [
        'name', 'type', 'status',
    ];

    public function enable() {
        $this->setAttribute('status', true);
        $this->save();
    }

    public function disable() {
        $this->setAttribute('status', true);
        $this->save();
    }

    public function notifiables() {
        return $this->morphToMany(
            'users',
            'model',
            'adv_not_model_has_users',
            'model_id',
            'user_id'
        );
    }

}
