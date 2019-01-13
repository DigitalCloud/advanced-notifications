<?php

namespace DigitalCloud\AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class Notifiable extends Model
{

    protected $fillable = [
        'notifiable_id', 'notifiable_type', 'model_id', 'model_type', 'status',
    ];

    public function enable() {
        $this->setAttribute('status', true);
        $this->save();
    }

    public function disable() {
        $this->setAttribute('status', true);
        $this->save();
    }

    public function notifiable() {
        $notifiableType = $this->notifiable_type;
        return $notifiableType::where('id', $this->notifiable_id)->first();
    }

}
