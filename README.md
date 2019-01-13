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

## Usage

To use advanced notification functionality, you need to extends your notification class from `DigitalCloud\AdvancedNotifications\Notification` class, which in tern extends the laravel `Illuminate\Notifications\Notification` class, so we can still benefit from original laravel notification functionality
 
 ```php
 
 
 namespace App\Notifications;
 
 // ...
 use DigitalCloud\AdvancedNotifications\Notification;
// ...
 
 class InvoicePaid extends Notification
 {
    // ...
    
 }


 ```

Then, add the `DigitalCloud\AdvancedNotifications\ControlledNotifications` trait to your Notifiable model(s):

```php

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DigitalCloud\AdvancedNotifications\ControlledNotifications;

class User extends Authenticatable
{
    use Notifiable, ControlledNotifications;

    // ...
}

```
Then you can do stuff like this:


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

