![Client flow diagram](https://raw.githubusercontent.com/CodeAdraas/php-redis-client/master/client-flow-diagram.png)

## Requirements
 * PHP 7.4+
 * [PhpRedis extension](https://github.com/phpredis/phpredis) (More connection schemes will be available in the future)

## Commands

* `ping`
* `get`
* `set`
* `append`
* `del`
* `unlink`
* `ex`

## Support commands

* `ix` Indicates the command should be executed when source does exist
* `nx` Indicates the command should be executed when source does NOT exist
* `json` Transforms the value to store in Redis in a json string format, or transform a received json string to a PHP object
* `exec` Executes command

## Basic usage

> Client options is not required as all important options have a default

* Create a client
```php
use Dodo\Redis\RedisClient;

$redis = new RedisClient([
    "scheme" => "redis",
    "host" => "...",
    "port" => "...",
    "debug" => true
]);
```
* Set command in combination with expiry time
```php
$redis->set("test", "this is a test")
    ->ex(2) // Expire in 2 seconds
    ->exec();

$query = $redis->get("test")

echo $query->get(); // 'this is a test'

sleep(2);

$query = $redis->get("test")

echo $query->get(); // Throws an error because the query response is a boolean: false
```

* `nx` and `ix` commands 
```php
$redis->set("test", "this is a test")->exec();

$redis->set("test", "this is NOT a test")
    ->nx() // If 'test' does not exist
    ->exec();

$query = $redis->get("test")

echo $query->get(); // 'this is a test'

$redis->set("test", "this is a NOT test")
    ->ix() // If 'test' does exist
    ->exec();

$query = $redis->get("test")

echo $query->get(); // 'this is NOT a test'
```

* Command with JSON
```php
$redis->set("test", array(
        "id" => uuid_create()
    ))
    ->json() // JSON stringyfies sent data
    ->ex(10)
    ->exec();

$query = $redis->get("test")
    ->json() // JSON decodes retrieved data
    ->exec();

$object = $query->get();

echo $object->id;
```