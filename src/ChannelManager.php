<?php

namespace DigitalCloud\AdvancedNotifications;

use Illuminate\Notifications\ChannelManager as IlluminateChannelManager;
use Illuminate\Contracts\Events\Dispatcher;
use \Illuminate\Contracts\Bus\Dispatcher as Bus;

class ChannelManager extends IlluminateChannelManager
{
    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|array|mixed $notifiables
     * @param  mixed $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        if($notification->disabled()) return false;

        return (new NotificationSender(
            $this, $this->app->make(Bus::class), $this->app->make(Dispatcher::class), $this->locale)
        )->send($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|array|mixed $notifiables
     * @param  mixed $notification
     * @param  array|null $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        if($notification->disabled()) return false;

        return (new NotificationSender(
            $this, $this->app->make(Bus::class), $this->app->make(Dispatcher::class), $this->locale)
        )->sendNow($notifiables, $notification, $channels);
    }
}
