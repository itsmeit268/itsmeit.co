<?php

declare (strict_types=1);
namespace Mihdan\IndexNow\Dependencies\GuzzleHttp\Promise;

/**
 * Exception that is set as the reason for a promise that has been cancelled.
 * @internal
 */
class CancellationException extends RejectionException
{
}
