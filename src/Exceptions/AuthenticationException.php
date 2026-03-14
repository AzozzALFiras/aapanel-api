<?php

namespace AzozzALFiras\AAPanelAPI\Exceptions;

/**
 * Thrown when authentication with the aaPanel API fails.
 *
 * Common causes: invalid API key, IP not whitelisted in panel settings,
 * or API interface not enabled in the panel.
 */
class AuthenticationException extends AaPanelException
{
    /**
     * Create from an HTTP status code (401/403).
     */
    public static function fromHttpCode(int $httpCode): self
    {
        return new self(
            "Authentication failed (HTTP {$httpCode}). Check your API key and IP whitelist.",
            $httpCode
        );
    }
}
