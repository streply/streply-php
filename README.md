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

Streply\Flush();
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

## Performance

### Creating transaction
```php
Streply\Performance::Start('transactionId', 'Product checkout');
```

### Adding point
```php
Streply\Performance::Point('transactionId', 'calculate price');

...

Streply\Performance::Point('transactionId', 'cart amount', [
    'amount' => 100.56
]);
```

### Sending transaction
```php
Streply\Performance::Finish('transactionId');
```

## Adding user data
```php
Streply\User('joey@streply.com');
```
or with parameters and name
```php
Streply\User('joey@streply.com', 'Joey Tribbiani', [
    'createdAt' => '2022-11-10 15:10:32'
]);
```

## Display logs

```php
<?php

print_r(Streply\Logs());
```