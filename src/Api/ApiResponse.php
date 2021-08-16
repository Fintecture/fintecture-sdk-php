<?php

namespace Fintecture\Api;

class ApiResponse
{
    public $result;
    public $error;
    public $errorMsg;

    public function __construct($response, $result)
    {
        $this->result = $result;
        $this->checkResult($response);
    }

    /**
     * Check content and status of response.
     *
      * @param object $response Response object
     */
    private function checkResult($response)
    {
        // Check JSON
        if (null === $this->result) {
            $this->setError("Response content is not valid json: \n\n{$response->getBody()->getContents()}");
            return;
        }

        // Check status and errors
        if (isset($this->result->status) && '200' !== $this->result->status) {
            $message = 'Error';
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
        $this->setError();
    }

    /**
     * Set error of ApiResponse.
     *
     * @param string $message Error message.
     */
    private function setError(string $message = '')
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
