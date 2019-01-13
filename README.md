# Laravel Advanced Notifications
This package allows you to manage system notifications in a database.

## Description
When working with systems which dealing with large number of customers, notification management become kind of complex issue, and a notifications management tool will be appreciated.
Here we introduce a powerful tool which allow you to manage notifications for your system. we offer api for enable and disable notifications on many levels as the following 
* you can disable notification globally, so the system will no longer send notifications of this type.
* you can disable a notification channel globally, so the system will no longer send notification on this channel.
* you can disable notification for a specific notifiable object on a specific notifications and/or channels.

## Installation

You can install the package via composer:

```bash
composer require digitalcloud/advanced-notifications
```

In Laravel 5.5 the service provider will automatically get registered. In older versions of the framework just add the service provider in config/app.php file:

```php

    'providers' => [
        DigitalCloud\AdvancedNotifications\AdvancedNotificationsServiceProvider::class,
    ];
    
```

You can publish the migration with:

```bash

php artisan vendor:publish --provider="DigitalCloud\AdvancedNotifications\AdvancedNotificationsServiceProvider" --tag="migrations"

```

After the migration has been published you can create advanced notification tables by running the migrations:

```bash

php artisan migrate

```

After install, you can do stuff like this:

```php

    // disable/enable all notifications for specific channel
    AdvancedNotifications::setChannelStatus('database', false); // true to enable
    
    // get specific channel status
    $channelStatus = AdvancedNotifications::getChannelStatus('database');
    
    //  disable/enable a specific notification globally- for all users.
    AdvancedNotifications::setNotificationStatus(InvoicePaid::class, false); // true to enable
    
    // get a specific notification status
    $notificationStatus = AdvancedNotifications::getNotificationStatus(InvoicePaid::class);

    // disable/enable a specific notification for a specific notifiable.
    $notifiable = \App\User::find(1);
    AdvancedNotifications::setNotificationStatusForNotifiable(InvoicePaid::class, $notifiable, false); // true to enable
    
    // get a specific notification status for a specific notifiable.
    $notificationStatusForNotifiable = AdvancedNotifications::getNotificationStatusForNotifiable(InvoicePaid::class, $notifiable);

    // disable/enable a specific channel for a specific notifiable.
    AdvancedNotifications::setChannelStatusForNotifiable('database', $notifiable, false); // true to enable
    
    // get a specific channel status for a specific notifiable.
    $channelStatusForNotifiable = AdvancedNotifications::getChannelStatusForNotifiable('database', $notifiable);

```

This package will respect the status of each channel, notification and notifiable, it will test the status of those types before actually sending the notification.

## Usage
##### Example 1:

It is best practice to leave the job of preparing the notifiable objects to notification itself. so using 
`getNotifiables()` and `setNotifiables()` will make your code more tidy and clean. a nice example of this:

```php
<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoicePaid extends Notification
{
    use Queueable;
    /**
     * @var User
     */
    private $notifiables;

    public function send() {
        if($this->getNotifiables()) {
            app(\Illuminate\Contracts\Notifications\Dispatcher::class)
                ->sendNow($this->getNotifiables(), $this);
        }
    }
    
    public function getNotifiables() {
            return $this->notifiables;
        }

    public function setNotifiables($notifiables) {
        $this->notifiables = $notifiables;
    }

}

```

##### Example 2: notification as event listener

we can make notification as event listener by adding the `handel()` function to the notification class.

```php

<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoicePaid extends Notification
{
    use Queueable;
    /**
     * @var User
     */
    private $notifiables;
    
    public function handle($event) {
        // setup notifiable objects, just for illustration
        // you can replace this code by your suitable logic
        if($event->notifiables) {
            $this->setNotifiables($event->notifiables);
        }

        // send the notification
        $this->send();
    }

    public function send() {
        if($this->getNotifiables()) {
            app(\Illuminate\Contracts\Notifications\Dispatcher::class)
                ->sendNow($this->getNotifiables(), $this);
        }
    }
    
    public function getNotifiables() {
        return $this->notifiables;
    }

    public function setNotifiables($notifiables) {
        $this->notifiables = $notifiables;
    }

}

```

then add this class as a listener in the $listen array in the app/providers/EventServiceProvider.php

```php

protected $listen = [
    \App\Events\NewPurchase::class => [InvoicePaid::class]
];

```

and you can fire the event when needed:

```php

event(new NewPurchase($args));

```

You can pass the notifiable objects to the event or you leave this job to the notification itself.


NewPurchase look like this:

```php

<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewPurchase
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notifiables;

    public function __construct($notifiables = null)
    {
        $this->notifiables = $notifiables;
    }
}
```
