<?php

namespace Krak\Mw;

/** composes a set of middleware into a callable to start the middleware chain
    This will create a middleware chain that fires the last middleware in the set *first*.
    Each middleware will be called with two parameters, the parameter and the next middleware.

    ```php
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

    $mw = composeMwSet([
        mw2(),
        mw1()
    ]);

    assert($mw(0) == 10);
    ```

    @param $mws an array of middleware
    @param $last the final next function to be called. If no middleware handle the request
        then the last middleware will be fired. Typically it should throw an exception
*/
function composeMwSet(array $mws, $last = null) {
    $last = $last ?: function($param) {
        throw new \RuntimeException("Last middleware was reached. No handlers were found.");
    };
    return array_reduce($mws, function($acc, $mw) {
        return function($param) use ($acc, $mw) {
            return $mw($param, $acc);
        };
    }, $last);
}
