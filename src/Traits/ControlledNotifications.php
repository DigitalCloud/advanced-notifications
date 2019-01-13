<?php

namespace DigitalCloud\AdvancedNotifications;

use DigitalCloud\AdvancedNotifications\Models\Channel;
use DigitalCloud\AdvancedNotifications\Models\Notifiable;

trait ControlledNotifications {

    public function notifiables() {
        return Notifiable::where('notifiable_id', $this->getAttribute('id'))
            ->where(['notifiable_type' => self::class])->get();
    }

    public function enabled($type) {
        $status = Notifiable::where('notifiable_id', $this->getAttribute('id'))
            ->where(['notifiable_type' => self::class])
            ->where(['model_type' => $type])
            ->first();
        return $status? $status->status : true;
    }

    public function enabledForNotification($notification) {
        $status = Notifiable::where('notifiable_id', $this->getAttribute('id'))
            ->where(['notifiable_type' => self::class])
            ->where(['model_type' => $notification])
            ->first();
        return $status? $status->status : true;
    }

    public function enabledForChannel($channel) {
        $status = Notifiable::where('notifiable_id', $this->getAttribute('id'))
            ->where(['notifiable_type' => self::class])
            ->where(['model_type' => $channel])
            ->first();
        return $status? $status->status : true;
    }


}
