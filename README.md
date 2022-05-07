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
        'release' => 'my-project-name@2.3.12',
        'environment' => 'production',
        'storeProvider' => new Streamly\Store\Providers\FileProvider(
            __DIR__ . '/store'
        )
    ]
);
```

Available providers:

- RequestProvider - Send requests immediately
- FileProvider - Storage requests in files and sends all requests after calling the function `Streamly\Close();`

## Exception

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

## Activity

```php
<?php

Streamly\Activity('message', 'channel', [
    'userId' => 1
]);
```

## Messages

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

## Display logs

```php
<?php

print_r(Streamly\Logs());
```