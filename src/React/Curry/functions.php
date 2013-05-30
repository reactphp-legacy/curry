<?php

namespace React\Curry;

use React\Curry\Placeholder;

function bind(/*$fn, $bound...*/)
{
    $bound = func_get_args();
    $fn = array_shift($bound);

    return function () use ($fn, $bound) {
        $args = func_get_args();

        if ($fn instanceof Placeholder) {
            $fn = $fn->resolve($args, 0);
        } elseif (is_array($fn)) {
            $fn = mergeParameters($fn, $args);
        }

        return call_user_func_array($fn, mergeAndAppendParameters($bound, $args));
    };
}

function …()
{
    return Placeholder::create();
}

function placeholder()
{
    return …();
}

/** @internal */
function mergeParameters(array $left, array &$right)
{
    foreach ($left as $position => &$param) {
        if ($param instanceof Placeholder) {
            $param = $param->resolve($right, $position);
        }
    }

    return $left;
}

/** @internal */
function mergeAndAppendParameters(array $left, array $right)
{
    $merged = mergeParameters($left, $right);

    return array_merge($merged, $right);
}
