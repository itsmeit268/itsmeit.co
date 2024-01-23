<?php

namespace Mihdan\IndexNow\Dependencies;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Mihdan\\IndexNow\\Dependencies\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
