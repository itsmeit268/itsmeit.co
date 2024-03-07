<?php

namespace Mihdan\IndexNow\Dependencies;

if (\class_exists('Mihdan\\IndexNow\\Dependencies\\Google_Client', \false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}
$classMap = ['Mihdan\\IndexNow\\Dependencies\\Google\\Client' => 'Mihdan\\IndexNow\\Dependencies\Google_Client', 'Mihdan\\IndexNow\\Dependencies\\Google\\Service' => 'Mihdan\\IndexNow\\Dependencies\Google_Service', 'Mihdan\\IndexNow\\Dependencies\\Google\\AccessToken\\Revoke' => 'Mihdan\\IndexNow\\Dependencies\Google_AccessToken_Revoke', 'Mihdan\\IndexNow\\Dependencies\\Google\\AccessToken\\Verify' => 'Mihdan\\IndexNow\\Dependencies\Google_AccessToken_Verify', 'Mihdan\\IndexNow\\Dependencies\\Google\\Model' => 'Mihdan\\IndexNow\\Dependencies\Google_Model', 'Mihdan\\IndexNow\\Dependencies\\Google\\Utils\\UriTemplate' => 'Mihdan\\IndexNow\\Dependencies\Google_Utils_UriTemplate', 'Mihdan\\IndexNow\\Dependencies\\Google\\AuthHandler\\Guzzle6AuthHandler' => 'Mihdan\\IndexNow\\Dependencies\Google_AuthHandler_Guzzle6AuthHandler', 'Mihdan\\IndexNow\\Dependencies\\Google\\AuthHandler\\Guzzle7AuthHandler' => 'Mihdan\\IndexNow\\Dependencies\Google_AuthHandler_Guzzle7AuthHandler', 'Mihdan\\IndexNow\\Dependencies\\Google\\AuthHandler\\AuthHandlerFactory' => 'Mihdan\\IndexNow\\Dependencies\Google_AuthHandler_AuthHandlerFactory', 'Mihdan\\IndexNow\\Dependencies\\Google\\Http\\Batch' => 'Mihdan\\IndexNow\\Dependencies\Google_Http_Batch', 'Mihdan\\IndexNow\\Dependencies\\Google\\Http\\MediaFileUpload' => 'Mihdan\\IndexNow\\Dependencies\Google_Http_MediaFileUpload', 'Mihdan\\IndexNow\\Dependencies\\Google\\Http\\REST' => 'Mihdan\\IndexNow\\Dependencies\Google_Http_REST', 'Mihdan\\IndexNow\\Dependencies\\Google\\Task\\Retryable' => 'Mihdan\\IndexNow\\Dependencies\Google_Task_Retryable', 'Mihdan\\IndexNow\\Dependencies\\Google\\Task\\Exception' => 'Mihdan\\IndexNow\\Dependencies\Google_Task_Exception', 'Mihdan\\IndexNow\\Dependencies\\Google\\Task\\Runner' => 'Mihdan\\IndexNow\\Dependencies\Google_Task_Runner', 'Mihdan\\IndexNow\\Dependencies\\Google\\Collection' => 'Mihdan\\IndexNow\\Dependencies\Google_Collection', 'Mihdan\\IndexNow\\Dependencies\\Google\\Service\\Exception' => 'Mihdan\\IndexNow\\Dependencies\Google_Service_Exception', 'Mihdan\\IndexNow\\Dependencies\\Google\\Service\\Resource' => 'Mihdan\\IndexNow\\Dependencies\Google_Service_Resource', 'Mihdan\\IndexNow\\Dependencies\\Google\\Exception' => 'Mihdan\\IndexNow\\Dependencies\Google_Exception'];
foreach ($classMap as $class => $alias) {
    \class_alias($class, $alias);
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 * @internal
 */
class Google_Task_Composer extends \Mihdan\IndexNow\Dependencies\Google\Task\Composer
{
}
/** @phpstan-ignore-next-line */
if (\false) {
    /** @internal */
    class Google_AccessToken_Revoke extends \Mihdan\IndexNow\Dependencies\Google\AccessToken\Revoke
    {
    }
    /** @internal */
    class Google_AccessToken_Verify extends \Mihdan\IndexNow\Dependencies\Google\AccessToken\Verify
    {
    }
    /** @internal */
    class Google_AuthHandler_AuthHandlerFactory extends \Mihdan\IndexNow\Dependencies\Google\AuthHandler\AuthHandlerFactory
    {
    }
    /** @internal */
    class Google_AuthHandler_Guzzle6AuthHandler extends \Mihdan\IndexNow\Dependencies\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    /** @internal */
    class Google_AuthHandler_Guzzle7AuthHandler extends \Mihdan\IndexNow\Dependencies\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    /** @internal */
    class Google_Client extends \Mihdan\IndexNow\Dependencies\Google\Client
    {
    }
    /** @internal */
    class Google_Collection extends \Mihdan\IndexNow\Dependencies\Google\Collection
    {
    }
    /** @internal */
    class Google_Exception extends \Mihdan\IndexNow\Dependencies\Google\Exception
    {
    }
    /** @internal */
    class Google_Http_Batch extends \Mihdan\IndexNow\Dependencies\Google\Http\Batch
    {
    }
    /** @internal */
    class Google_Http_MediaFileUpload extends \Mihdan\IndexNow\Dependencies\Google\Http\MediaFileUpload
    {
    }
    /** @internal */
    class Google_Http_REST extends \Mihdan\IndexNow\Dependencies\Google\Http\REST
    {
    }
    /** @internal */
    class Google_Model extends \Mihdan\IndexNow\Dependencies\Google\Model
    {
    }
    /** @internal */
    class Google_Service extends \Mihdan\IndexNow\Dependencies\Google\Service
    {
    }
    /** @internal */
    class Google_Service_Exception extends \Mihdan\IndexNow\Dependencies\Google\Service\Exception
    {
    }
    /** @internal */
    class Google_Service_Resource extends \Mihdan\IndexNow\Dependencies\Google\Service\Resource
    {
    }
    /** @internal */
    class Google_Task_Exception extends \Mihdan\IndexNow\Dependencies\Google\Task\Exception
    {
    }
    /** @internal */
    interface Google_Task_Retryable extends \Mihdan\IndexNow\Dependencies\Google\Task\Retryable
    {
    }
    /** @internal */
    class Google_Task_Runner extends \Mihdan\IndexNow\Dependencies\Google\Task\Runner
    {
    }
    /** @internal */
    class Google_Utils_UriTemplate extends \Mihdan\IndexNow\Dependencies\Google\Utils\UriTemplate
    {
    }
}
