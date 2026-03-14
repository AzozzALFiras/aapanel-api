<?php

namespace AzozzALFiras\AAPanelAPI\Exceptions;

use RuntimeException;

/**
 * Base exception for all aaPanel API errors.
 *
 * Provides access to the API response data when available,
 * allowing callers to inspect the raw panel response.
 */
class AaPanelException extends RuntimeException
{
    /** @var array|null Raw API response */
    private $response;

    /**
     * @param string          $message  Error description
     * @param int             $code     Error code
     * @param \Throwable|null $previous Previous exception
     * @param array|null      $response Raw API response data
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, ?array $response = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Get the raw API response data, if available.
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }
}
