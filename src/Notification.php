<?php

namespace DigitalCloud\AdvancedNotifications;

use DigitalCloud\AdvancedNotifications\Models\Notifiable;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Notifications\Notification as IlluminateNotification;

class Notification extends IlluminateNotification
{
    protected $enabled = true;
    protected $notifiable;

    public function enable()
    {
        $this->status()->enable();
        return $this;
    }

    public function disable()
    {
        $this->status()->disable();
        return $this;
    }

    public function enabled()
    {
        return $this->status()? $this->status()->status : true;
    }


    public function disabled()
    {
        return !$this->enabled();
    }

    /*public function send() {
        if($this->getNotifiable()) {
            app(Dispatcher::class)->send($this->getNotifiable(), $this);
        }

    }*/

    public function getNotifiable() {
        return  Notifiable::where('status', true)
            ->where(['model_type' => get_class($this)])->get()->map(function ($notifiable) {
                return $notifiable->notifiable();
            })->filter()->all();
    }

    public function setNotifiable($notifiable) {
        $this->notifiable = $notifiable;
    }

    public function status() {
        return \DigitalCloud\AdvancedNotifications\Models\Notification::where('type', get_class($this))->first();
    }

}
