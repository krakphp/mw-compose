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

describe('Mw Compose', function() {
    describe('#composeMwSet', function() {
        it('composes middleware into one middleware callable', function() {
            $mw = Krak\Mw\composeMwSet([
                mw2(),
                mw1()
            ]);

            assert($mw(0) == 10);
        });
        it('throws an exception if no middleware handle the parameter', function() {
            $mw = Krak\Mw\composeMwSet([]);

            try {
                $mw(0);
            } catch (RuntimeException $e) {
                return;
            }

            assert(0);
        });
    });
});
