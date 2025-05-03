<?php

namespace ISend\SMS\Exceptions;

use Exception;
use Throwable;

class ISendException extends Exception
{
    /**
     * API response data.
     */
    protected ?array $responseData;

    /**
     * The request data that caused the exception.
     */
    protected ?array $requestData;

    /**
     * The HTTP status code.
     */
    protected int $statusCode;

    /**
     * Create a new ISend exception instance.
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array|null $responseData Response data from the API
     * @param Throwable|null $previous Previous exception
     * @param array|null $requestData Request data sent to the API
     */
    public function __construct(
        string $message,
        int $statusCode = 0,
        ?array $responseData = null,
        ?Throwable $previous = null,
        ?array $requestData = null
    ) {
        parent::__construct($message, $statusCode, $previous);
        
        $this->statusCode = $statusCode;
        $this->responseData = $responseData;
        $this->requestData = $requestData;
    }

    /**
     * Get the API response data.
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }
    
    /**
     * Get the request data that caused the exception.
     */
    public function getRequestData(): ?array
    {
        return $this->requestData;
    }
    
    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * Check if this is a server error (5xx status code).
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }
    
    /**
     * Check if this is a client error (4xx status code).
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }
    
    /**
     * Check if this exception is due to authentication failure.
     */
    public function isAuthenticationError(): bool
    {
        return $this->statusCode === 401;
    }
    
    /**
     * Check if this exception is due to authorization failure.
     */
    public function isAuthorizationError(): bool
    {
        return $this->statusCode === 403;
    }
    
    /**
     * Check if this exception is due to resource not found.
     */
    public function isNotFoundError(): bool
    {
        return $this->statusCode === 404;
    }
    
    /**
     * Check if this exception is due to validation error.
     */
    public function isValidationError(): bool
    {
        return $this->statusCode === 422;
    }
    
    /**
     * Get a human-readable representation of the exception, useful for debugging.
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'status_code' => $this->statusCode,
            'response_data' => $this->responseData,
            'request_data' => $this->requestData,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}