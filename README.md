# Streply PHP SDK

## Install

```
composer require streply/streply-php
```
## Initialization

Initialize Streply on beginning your code and close the connection after your code.<br>
Both functions are mandatory for correctly working.

```php
<?php

Streply\Initialize('https://clientPublicKey@api.streply.com/projectId');

// Your code here

Streply\Close();
```

Where:

- `clientPublicKey` your public API key
- `projectId` your project ID

Initialization with parameters

```php
<?php

Streply\Initialize(
    'https://clientPublicKey@api.streply.com/projectId',
    [
        'release' => 'my-project-name@2.3.12',
        'environment' => 'production',
    ]
);
```

### Change store provider

```php
<?php

Streply\Initialize(
    'https://clientPublicKey@api.streply.com/projectId',
    [
        'storeProvider' => new Streply\Store\Providers\FileProvider(
            __DIR__ . '/store'
        )
    ]
);
```

Available providers:

- RequestProvider - Send requests immediately
- FileProvider - Storage requests in files and sends all requests after calling the function `Streply\Close();`

### Filter events before send

```php
<?php

Streply\Initialize(
    'https://clientPublicKey@api.streply.com/projectId',
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

### Turn off Streply internal requests

```php
<?php

Streply\Initialize(
    'https://clientPublicKey@api.streply.com/projectId',
    [
        'internalRequests' => false
    ]
);
```

## Capture

### Exception

```php
<?php

try {
    if(true) {
        throw new \Exceptions\SomeException('Exception message here');
    }
} catch(\Exceptions\ParentException $exception) {
    Streply\Exception($exception);
}
```

### Exception with params and capture level

```php
<?php

use Streply\Enum\Level;

try {
    if(true) {
        throw new \Exceptions\SomeException('Exception message here');
    }
} catch(\Exceptions\ParentException $exception) {
    Streply\Exception(
        $exception,
        [
            'paramName' => 'paramValue'
        ],
        Level::CRITICAL
    );
}
```

### Activity

```php
<?php

Streply\Activity(
    'message', 
    [
        'paramName' => 'paramValue'
    ],
    '#optionalChannel' 
);
```

### Log

```php
<?php

Streply\Log(
    'log.name', 
    [
        'paramName' => 'paramValue'
    ],
    '#optionalChannel',
    Level::CRITICAL 
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

use Streply\Enum\BreadcrumbType;

Streply\Activity('someActivity');

Streply\Breadcrumb(BreadcrumbType::INFO, 'firstBreadcrumb for someActivity');
Streply\Breadcrumb(BreadcrumbType::DEBUG, 'secondBreadcrumb for someActivity', [
    'parameterName' => 'parameterValue'
]);
```

Available types: `BreadcrumbType::INFO`, `BreadcrumbType::DEBUG`, `BreadcrumbType::ERROR` and `BreadcrumbType::QUERY`.

## Display logs

```php
<?php

print_r(Streply\Logs());
```