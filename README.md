# Laravel Advanced Notifications
This package allows you to manage system notifications in a database.

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

##### Example 2: as event listener

we can make notification as event listener by adding the handel() function to the notification class.

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
        if($event->notifiables) {
            $this->setNotifiables($event->notifiables);
        }

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
