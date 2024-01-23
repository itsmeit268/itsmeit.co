<?php

namespace Mihdan\IndexNow\Dependencies;

// Don't redefine the functions if included multiple times.
if (!\function_exists('Mihdan\\IndexNow\\Dependencies\\GuzzleHttp\\uri_template')) {
    require __DIR__ . '/functions.php';
}
