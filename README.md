Yii2 Locker Extension
=====================

Extension includes following features:

- Activate/Deactivate resource lock by unique identifier.
- Check and block request if exists active lock.

For license information check the [LICENSE](LICENSE.md)-file.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist notamedia/yii2-locker
```

or add

```
"notamedia/yii2-locker": "1.0.0"
```

to the require section of your composer.json.

Configuration
-------------

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        //....
        'lockManager' => [
            'class' => LockManager::class,
            'lockTime' => [
                LockManager::DEFAULT_LOCK_TIME_KEY => 900,
            ]
        ],
        //....
    ],
    //....
];
```

* Note: you may set custom time for each resource, simple add to `lockTime`, key - resource class and 
value - time in seconds
* Note: if your db driver non mysql, you need set custom `'initTimeExpressionValue' = '...'` and 
`'diffExpressionValue' = '...'`
* Note: if you want to use custom lock class, you need set new `LockInterface::class` definitions like
```
'container' => [
    '...',
    'definitions' => [
        '...',
        LockInterface::class => function ($container, $params, $config) {
            list($user, $resource) = $params;
            return Lock::findOrCreate($user, $resource);
        }
        '...'
    ],
    '...'
]
```

connect following actions in your controllers and configure routing

```php
return [
    //....
    'lock' => [
        'class' => LockAction::class,
        'modelClass' => $this->modelClass,
        'checkAccess' => [$this, 'checkAccess']
    ],
    'unlock' => [
        'class' => UnlockAction::class,
        'modelClass' => $this->modelClass,
        'checkAccess' => [$this, 'checkAccess'],
    ],
    //....
];
```

* Note: you may set `'lockManager' = '...'` attribute if your LockManager component has other key

attach behavior to check lock, or you can check by yourself

```php
return [
    //....
    [
        'class' => LockControlFilter::class,
        'only' => ['update']
    ],
    //....
];
```

* Note: you may set `'lockManager' = '...'` attribute if your LockManager component has other key

connect and execute [migration](/src/migrations/m000000_000000_create_table_lock.php), example:

For yii2 > 2.0.10

```php
'controllerMap' => [
    //...
    'migrate' => [
        'class' => MigrateController::class,
        'migrationNamespaces' => [
            'notamedia\locker\migrations',
        ],
        //...
    ],
    //...
 ]
```

For yii2 < 2.0.10 create new migration and use extends

```php
class mxxxxxx_xxxxxx_create_table_lock extends m000000_000000_create_table_lock
```

Usage
-----

Methods:
* src/rest/LockAction.php - activates lock and returns `204` status code if successful
* src/rest/UnlockAction.php - deactivates lock and returns `204` status code if successful

Exceptions
----------

* src/LockAnotherUserException.php - exception if lock set another user, status code `500`
* src/LockNotExistException.php - exception if lock not exist, status code `500`
* src/LockNotExpiredException.php - exception if lock actual and its time not expired, status code `500`