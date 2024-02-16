# Streply PHP SDK

## Install

```
composer require streply/streply-php
```
## Initialization

Initialize Streply on beginning your code.

```php
Streply\Initialize('https://clientPublicKey@api.streply.com/projectId');
```

Where:

- `clientPublicKey` your public API key
- `projectId` your project ID

Initialization with parameters

```php
Streply\Initialize(
    'https://clientPublicKey@api.streply.com/projectId',
    [
        'release' => 'my-project-name@2.3.12',
        'environment' => 'production',
    ]
);
```

## Capture

### Exception

```php
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

### Log

```php
Streply\Log('log.name', ['paramName' => 'paramValue']);
```

### Activity

```php
Streply\Activity('message', ['paramName' => 'paramValue']);
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

## Configuration

### Scopes

The `setScope` helper will set up the scope for all events captured by the Streply SDK.

```php
\Streply\setScope(function (\Streply\Scope $scope): void {
    $scope->setChannel('my-chanel');
});
```

If you want to change the scope for a single event, you can use the withScope helper instead. This helper does not retain the scope changes made.

```php
\Streply\withScope(function (\Streply\Scope $scope): void {
    $scope->setChannel('my-chanel');
    
    \Streply\Log('my log with channel');
});
```

Available methods in scope:

- `setChannel`
- `setFlag`
- `setRelease`
- `setEnvironment`

### Filter events before send

```php
Streply\Initialize(
    'https://clientPublicKey@api.streply.com/projectId',
    [
        'filterBeforeSend' => function(\Streply\Entity\Event $event): bool {
            if($event->getMessage() === 'someMessage') {
                return false;
            }
            
            return true;
        }
    ]
);
```

Additionally, you have the flexibility to modify all choices at a later time:

```php
Streply\Configuration::filterBeforeSend(function(\Streply\Entity\Event $event) {
    if($event->getMessage() === 'someMessage') {
        return false;
    }

    return true;
});
```

### Ignore Exceptions

```php
Streply\Configuration::ignoreExceptions([
    App\Exception\QueryException::class,
    App\Exception\InvalidAuthorizationException::class,
]);
```

## Display logs

```php
print_r(Streply\Logs());
```