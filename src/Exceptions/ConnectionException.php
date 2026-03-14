<?php

namespace AzozzALFiras\AAPanelAPI\Exceptions;

/**
 * Thrown when the HTTP connection to the aaPanel server fails.
 *
 * Common causes: network timeout, DNS failure, invalid panel URL,
 * SSL certificate issues, or panel service is down.
 */
class ConnectionException extends AaPanelException
{
    /**
     * Create from a cURL error message.
     */
    public static function fromCurlError(string $curlError): self
    {
        return new self("cURL error: {$curlError}");
    }

    /**
     * Create from an invalid JSON response.
     */
    public static function fromInvalidJson(string $jsonError, string $rawOutput): self
    {
        return new self(
            "Invalid JSON response: {$jsonError} | Raw: " . substr($rawOutput, 0, 500)
        );
    }
}
