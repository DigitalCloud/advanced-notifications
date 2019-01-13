<?php

namespace DigitalCloud\AdvancedNotifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\NotificationSender as IlluminateNotificationSender;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Str;

class NotificationSender extends IlluminateNotificationSender
{
    /**
     * Create a new notification sender instance.
     *
     * @param  \Illuminate\Notifications\ChannelManager  $manager
     * @param  \Illuminate\Contracts\Bus\Dispatcher  $bus
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  string|null  $locale
     * @return void
     */
    public function __construct($manager, $bus, $events, $locale = null)
    {
        parent::__construct($manager, $bus, $events, $locale);
    }

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        if ($notification instanceof ShouldQueue) {
            $this->queueNotification($notifiables, $notification);
        }

        return $this->sendNow($notifiables, $notification);
    }

    /**
     * Queue the given notification instances.
     *
     * @param  mixed  $notifiables
     * @param  array[\Illuminate\Notifications\Channels\Notification]  $notification
     * @return void
     */
    protected function queueNotification($notifiables, $notification)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        $original = clone $notification;

        foreach ($notifiables as $notifiable) {
            if(!AdvancedNotifications::getNotificationStatusForNotifiable(get_class($notification), $notifiable)) continue;

            $notificationId = Str::uuid()->toString();

            foreach ($original->queue($notifiable) as $channel => $config) {
                if(is_numeric($channel)){
                    $channel = $config;
                    if(!AdvancedNotifications::getChannelStatus($channel) || !AdvancedNotifications::getChannelStatusForNotifiable($channel, $notifiable)) continue;
                    $connection = $notification->connection;
                    $queue = $notification->queue;
                    $delay = $notification->delay;
                }else{
                    if(!AdvancedNotifications::getChannelStatus($channel) || !AdvancedNotifications::getChannelStatusForNotifiable($channel, $notifiable)) continue;
                    $connection = array_get($config,'connection',$notification->connection);
                    $queue = array_get($config,'queue',$notification->queue);
                    $delay = array_get($config,'delay',$notification->delay);
                }
                $notification = clone $original;

                $notification->id = $notificationId;

                if (! is_null($this->locale)) {
                    $notification->locale = $this->locale;
                }

                $this->bus->dispatch(
                    (new SendQueuedNotifications($notifiable, $notification, [$channel]))
                        ->onConnection($connection)
                        ->onQueue($queue)
                        ->delay($delay)
                );
            }
        }
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null){
        $notifiables = $this->formatNotifiables($notifiables);

        $original = clone $notification;
        foreach ($notifiables as $notifiable) {
            if(!AdvancedNotifications::getNotificationStatusForNotifiable(get_class($notification), $notifiable)) continue;
            if (empty($viaChannels = $channels ?: $notification->via($notifiable))) {
                continue;
            }

            $this->withLocale($this->preferredLocale($notifiable, $notification), function () use ($viaChannels, $notifiable, $original) {
                $notificationId = Str::uuid()->toString();

                foreach ((array) $viaChannels as $channel) {
                    if(!AdvancedNotifications::getChannelStatus($channel) || !AdvancedNotifications::getChannelStatusForNotifiable($channel, $notifiable)) continue;
                    $this->sendToNotifiable($notifiable, $notificationId, clone $original, $channel);
                }
            });
        }
    }

}
