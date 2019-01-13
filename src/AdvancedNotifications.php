<?php

namespace DigitalCloud\AdvancedNotifications;


use DigitalCloud\AdvancedNotifications\Models\Channel;
use DigitalCloud\AdvancedNotifications\Models\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class AdvancedNotifications
{

    public static $channels = [];

    public static $groups = [];

    public static $notifications = [];


    public static function availableChannels()
    {
        return collect(static::$channels)->all();
    }

    public static function channels(array $channels)
    {
        static::$channels = array_merge(static::$channels, $channels);

        return new static;
    }

    public static function availableNotifications()
    {
        return collect(static::$notifications)->all();
    }

    public static function notifications(array $notifications)
    {
        static::$notifications = array_merge(static::$notifications, $notifications);

        return new static;
    }

    public static function notificationsIn($directory)
    {
        $namespace = app()->getNamespace();

        $notifications = [];

        foreach ((new Finder())->in($directory)->files() as $notification) {
            $notification = $namespace.str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    Str::after($notification->getPathname(), app_path().DIRECTORY_SEPARATOR)
                );

            if (is_subclass_of($notification, \Illuminate\Notifications\Notification::class) &&
                ! (new \ReflectionClass($notification))->isAbstract()) {
                $notifications[] = $notification;
            }
        }

        static::notifications(
            collect($notifications)->sort()->all()
        );
    }

    public static function availableGroups()
    {
        return collect(static::$groups)->all();
    }

    public static function groups(array $groups)
    {
        static::$groups = array_merge(static::$groups, $groups);

        return new static;
    }

    public static function isInstalled()
    {
        if (!env('APP_KEY'))
            return true;

        return
            Schema::hasTable('adv_not__notifications');
    }


    public static function channelEnabled($channel) {
        $status = Channel::where('name', $channel)->first();
        return $status? $status->status : true;
    }

    public static function setChannelStatus($channel, $status) {
        Channel::updateOrCreate(['name' => $channel], ['status' => $status]);
    }

    public static function getChannelStatus($channel) {
        $channel = Channel::where('name', $channel)->first();
        return $channel? $channel->status : true;
    }

    public static function setNotificationStatus($notification, $status) {
        return \DigitalCloud\AdvancedNotifications\Models\Notification::updateOrCreate(['name' => $notification], ['status' => $status]);
    }

    public static function getNotificationStatus($notification) {
        $notification = \DigitalCloud\AdvancedNotifications\Models\Notification::where('name', $notification)->first();
        return $notification? $notification->status : true;
    }

    public static function setChannelStatusForNotifiable($channel, $notifiable, $status) {
        return Notifiable::updateOrCreate(['notifiable_id' => $notifiable->getAttribute('id'),
            'notifiable_type' => get_class($notifiable),
            'model_type' => $channel], ['status' => $status]);
    }

    public static function getChannelStatusForNotifiable($channel, $notifiable) {
        $status = Notifiable::where('notifiable_id', $notifiable->getAttribute('id'))
            ->where(['notifiable_type' => get_class($notifiable)])
            ->where(['model_type' => $channel])
            ->first();
        return $status? $status->status : true;
    }

    public static function setNotificationStatusForNotifiable($notification, $notifiable, $status) {
        return Notifiable::updateOrCreate(['notifiable_id' => $notifiable->getAttribute('id'),
            'notifiable_type' => get_class($notifiable),
            'model_type' => $notification], ['status' => $status]);
    }

    public static function getNotificationStatusForNotifiable($notification, $notifiable) {
        $status = Notifiable::where('notifiable_id', $notifiable->getAttribute('id'))
            ->where(['notifiable_type' => get_class($notifiable)])
            ->where(['model_type' => $notification])
            ->first();
        return $status? $status->status : true;
    }

}
