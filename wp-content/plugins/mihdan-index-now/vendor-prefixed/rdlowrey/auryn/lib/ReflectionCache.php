<?php

namespace Mihdan\IndexNow\Dependencies\Auryn;

/** @internal */
interface ReflectionCache
{
    public function fetch($key);
    public function store($key, $data);
}
