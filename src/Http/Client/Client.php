<?php
namespace Concrete\Core\Http\Client;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareTrait;
use Zend\Http\Client as ZendClient;
use Zend\Http\Request as ZendRequest;
use Zend\Uri\Http as ZendUriHttp;
use Concrete\Core\Logging\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Exception;
use Throwable;

class Client extends ZendClient implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * Get the currently configured logger.
     *
     * @return LoggerInterface|null
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_NETWORK;
    }

    /**
     * Set the currently configured logger.
     *
     * @param LoggerInterface|null $value
     *
     * @return static
     */
    public function setLogger(LoggerInterface $value = null)
    {
        $this->logger = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ZendClient::send()
     */
    public function send(ZendRequest $request = null)
    {
        $logger = $this->getLogger();
        try {
            $response = parent::send($request);
        } catch (Throwable $e) {
            $this->logError($e, 'send');

            // Rethrow the caught throwable
            throw $e;
        }

        if ($logger !== null) {
            $statusCode = $response->getStatusCode();

            try {
                $body = $response->getBody();
            } catch (Throwable $x) {
                $body = '';
            }

            if (mb_strlen($body) <= 200) {
                $shortBody = $body;
            } else {
                $shortBody = mb_substr($body, 0, 197) . '...';
            }

            $logger->debug(
                'The response code was {statusCode} and the body was {shortBody}',
                [
                    'statusCode' => $statusCode,
                    'headers' => $response->getHeaders()->toArray(),
                    'shortBody' => $shortBody,
                    'body' => $body,
                ]
            );
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @see ZendClient::doRequest()
     */
    protected function doRequest(ZendUriHttp $uri, $method, $secure = false, $headers = [], $body = '')
    {
        $logger = $this->getLogger();
        if ($logger !== null) {
            $uriString = (string) $uri;
            if (mb_strlen($body) <= 200) {
                $shortBody = (string) $body;
            } else {
                $shortBody = mb_substr($body, 0, 197) . '...';
            }
            $logger->debug(
                'Sending {method} request to {uri} with body {shortBody}',
                [
                    'uri' => $uriString,
                    'method' => $method,
                    'headers' => is_array($headers) ? $headers : (array) $headers,
                    'shortBody' => $shortBody,
                    'body' => $body,
                ]
            );
        }

        try {
            return parent::doRequest($uri, $method, $secure, $headers, $body);
        } catch (Throwable $e) {
            $this->logError($e, 'doRequest');

            // Rethrow the caught throwable
            throw $e;
        }

    }

    /**
     * Report a caught error to the logger if one is available
     *
     * @param Throwable $e
     * @param $action
     */
    protected function logError(Throwable $e, $action)
    {
        $logger = $this->getLogger();

        if ($logger) {
            $this->logger->error(
                'Failed to send request: {error}',
                [
                    'action' => $action,
                    'error' => (string) $e,
                    'message' => $e->getMessage(),
                    'error_class' => get_class($e)
                ]
            );
        }
    }
}
