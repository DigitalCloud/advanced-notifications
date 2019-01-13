<?php

namespace DigitalCloud\AdvancedNotifications\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    
    protected $fillable = [
        'name', 'status',
    ];

    public function enable() {
        $this->setAttribute('status', true);
        $this->save();
    }

    public function disable() {
        $this->setAttribute('status', true);
        $this->save();
    }

}
