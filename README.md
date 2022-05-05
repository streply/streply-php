# Streamly PHP SDK

## Install

```
composer require streamly/streamly
```
## Initialization

```
<?php

Streamly\Initialize(
    'https://clientPublicKey@api.thestreamly.com/projectId',
    [
        'release' => 'my-project-name@2.3.12',
        'environment' => 'production',
    ]
);
```

## Exception

```
<?php

try {
    if(true) {
        throw new \Exceptions\SomeException('Invalid some action');
    }
} catch(\Exceptions\ParentException $exception) {
    $output = Streamly\Exception($exception);
}
```

## Activity

```
<?php

Streamly\Activity('someUserId', 'users.login');
```

## Messages

```
<?php

Streamly\Message(
    'testMessage',
    [
        'userId' => 133423
    ],
    'users',
    \Streamly\Enum\Level::CRITICAL
);
```

## Display logs

```
print_r(Streamly\Logs());
```