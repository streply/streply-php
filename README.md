# Streamly PHP SDK

## Install

```
composer require streamly/streamly
```
## Initialization

```php
<?php

Streamly\Initialize(
    'https://clientPublicKey@api.thestreamly.com/1',
    [
        'release' => 'my-project-name@2.3.12',
        'environment' => 'production',
    ]
);
```

### Change provider

```php
<?php

Streamly\Initialize(
    'https://clientPublicKey@api.thestreamly.com/1',
    [
        'storeProvider' => new Streamly\Store\Providers\FileProvider(
            __DIR__ . '/store'
        )
    ]
);
```

Available providers:

- RequestProvider - Send requests immediately
- FileProvider - Storage requests in files and sends all requests after calling the function `Streamly\Close();`

### Filter events before send

```php
<?php

use Streamly\Entity\Event;

Streamly\Initialize(
    'http://clientPublicKey@api.streamly.local:8888/123',
    [
        'filterBeforeSend' => function(Event $event): bool {
            if($event->getMessage() === 'someMessage') {
                return false;
            }
            
            return true;
        }
    ]
);
```

## Capture

### Exception

```php
<?php

try {
    if(true) {
        throw new \Exceptions\SomeException('Invalid some action');
    }
} catch(\Exceptions\ParentException $exception) {
    Streamly\Exception($exception);
}
```

### Activity

```php
<?php

Streamly\Activity('message', 'channel', [
    'userId' => 1
]);
```

### Messages

```php
<?php

Streamly\Message(
    'testMessage',
    [
        'userId' => 133423
    ],
    'users',
    Streamly\Enum\Level::CRITICAL
);
```

### Capture levels

- `Level::CRITICAL`
- `Level::HIGH`
- `Level::NORMAL`
- `Level::LOW`

## Breadcrumbs

```php
<?php

use Streamly\Enum\BreadcrumbType;

Streamly\Activity('someActivity');

Streamly\Breadcrumb(BreadcrumbType::INFO, 'firstBreadcrumb for someActivity');
Streamly\Breadcrumb(BreadcrumbType::DEBUG, 'secondBreadcrumb for someActivity', [
    'parameterName' => 'parameterValue'
]);
```

Available types: `BreadcrumbType::INFO`, `BreadcrumbType::DEBUG`, `BreadcrumbType::ERROR` and `BreadcrumbType::QUERY`.

## Display logs

```php
<?php

print_r(Streamly\Logs());
```