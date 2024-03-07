<?php

namespace Mihdan\IndexNow\Dependencies\GuzzleHttp;

use Mihdan\IndexNow\Dependencies\Psr\Http\Message\MessageInterface;
/** @internal */
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message) : ?string;
}
