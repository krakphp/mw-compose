# Mw Compose

Mw Compose is a simple utility library for composing/creating middleware. The algorithm is written generically, so this middleware system can be used in any type of application.

The main function for creating middleware is `Krak\Mw\composeMwSet`.

A **middleware** is any php callable that accepts two arguments: the **parameter** and the next middleware in the set.

The **parameter** can be anything specific to your application. In the `krak/mw` http framework, the parameter is `Psr\Http\Message\ServerRequestInterface`, but your application can make it an array, a tuple, an object, a scalar and so on.

## API

```
// composes a middleware set into a single executable middleware executed in LIFO order
// So, the *last* middleware in the set will be the *first* to execute
callable Krak\Mw\mwComposeSet(array $mws, $last = null);
```


## When to use middleware

Middleware implement the decorator pattern with a little more elegance. Typically, if you want to use decoration but want to easily add decorators in any order, then using the middleware pattern is the best choice. With decoration, defining services can be cumbersome if you have a lot of services you want to decorate; using middleware, you can simply create your middleware services and then compose them and you don't have to worry about constructing each service with last decorated service.

## Example

Here's a simple example of the middleware pattern in use

```php
<?php

function mw1() {
    return function($param, $next) {
        return $next($param < 5 ? 5 : $param);
    };
}
function mw2() {
    return function($param, $next) {
        return $param + 5;
    };
}

// LIFO execution
$mw = Krak\Mw\composeMwSet([
    mw2(),
    mw1()
]);

assert($mw(0) == 10);
```

As you can see, the first middleware simply "decorated" the parameter and just passed to the next handler which then actually returned a response.

### Decoration Example

```php
<?php

function decorated1($handler) {
    return function($param) use ($handler) {
        return $handler($param < 5 ? 5 : $param);
    };
}

function decorated2() {
    return function($param) {
        return $param + 5;
    };
}

$handler = decorated2();
$handler = decorated1($handler);
assert($handler(0) == 10);
```

As you can see, if any decorators required additional dependencies or if you had more decorators, then the creation of the final decorated handler can be cumbersome.
