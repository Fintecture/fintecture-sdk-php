<?php

namespace Fintecture\Api;

use Psr\Http\Message\ResponseInterface;

class ApiResponse
{
    /** @var ?object $result */
    public $result;

    /** @var bool $error */
    public $error;

    /** @var string $errorMsg */
    public $errorMsg;

    /** @param ?object $result */
    public function __construct(ResponseInterface $response, $result)
    {
        $this->result = $result;
        $this->checkResult($response);
    }

    /**
     * Check content and status of response.
     *
      * @param object $response Response object
     */
    private function checkResult($response): void
    {
        // Check status and errors
        $statusCode = $response->getStatusCode();
        if (!in_array($statusCode, [200, 204])) {
            $message = 'Error - Status code' . $statusCode;
            if (isset($this->result->errors)) {
                foreach ($this->result->errors as $error) {
                    if (isset($error->message)) {
                        $message .= ': ' . $error->message;
                    } elseif (isset($error->detail)) {
                        $message .= ': ' . $error->detail;
                    } elseif (isset($error->title)) {
                        $message .= ': ' . $error->title;
                    }
                }
            }
            $this->setError($message);
            return;
        }
        $this->setError(); // no error
    }

    /**
     * Set error of ApiResponse.
     *
     * @param string $message Error message.
     */
    private function setError(string $message = ''): void
    {
        $this->error = !empty($message);
        $this->errorMsg = $this->error ? $message : '';
    }

    /**
     * Override __get function to get the requested result property.
     *
     * @param string $name Name of the result property.
     *
     * @throws \Exception if property is not defined
     */
    public function __get(string $name)
    {
        if (property_exists($this->result, $name)) {
            return $this->result->$name;
        }
        throw new \Exception('Undefined API property: ' . static::class . '::$' . $name);
    }
}
