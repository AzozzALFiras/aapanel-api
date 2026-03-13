<?php

namespace AzozzALFiras\AAPanelAPI;

use AzozzALFiras\AAPanelAPI\Exceptions\ConnectionException;
use AzozzALFiras\AAPanelAPI\Exceptions\AuthenticationException;
use CURLFile;

class HttpClient
{
    private $apiKey;
    private $baseUrl;
    private $cookieFile;
    private $timeout;
    private $verifySsl;

    public function __construct(string $apiKey, string $baseUrl, array $options = [])
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $options['timeout'] ?? 60;
        $this->verifySsl = $options['verify_ssl'] ?? false;

        $cookieDir = $options['cookie_dir'] ?? sys_get_temp_dir();
        $this->cookieFile = $cookieDir . '/' . md5($this->baseUrl) . '.cookie';

        if (!file_exists($this->cookieFile)) {
            touch($this->cookieFile);
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Generate authentication signature data.
     */
    public function generateRequestData(): array
    {
        $now = time();
        return [
            'request_token' => md5($now . md5($this->apiKey)),
            'request_time'  => $now,
        ];
    }

    /**
     * Send POST request to panel API.
     *
     * @param string $endpoint API endpoint path (e.g. '/system?action=GetSystemTotal')
     * @param array  $data     Additional POST data
     * @return array Decoded JSON response
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    public function post(string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $postData = array_merge($this->generateRequestData(), $data);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_COOKIEJAR      => $this->cookieFile,
            CURLOPT_COOKIEFILE     => $this->cookieFile,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
        ]);

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($output === false) {
            throw new ConnectionException("cURL error: {$error}");
        }

        if ($httpCode === 401 || $httpCode === 403) {
            throw new AuthenticationException("Authentication failed (HTTP {$httpCode}). Check your API key and IP whitelist.");
        }

        $decoded = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConnectionException("Invalid JSON response: " . json_last_error_msg() . " | Raw: " . substr($output, 0, 500));
        }

        return $decoded;
    }

    /**
     * Send POST request with file upload.
     */
    public function postWithFile(string $endpoint, array $data, string $fileField, string $filePath, string $fileName): array
    {
        $url = $this->baseUrl . $endpoint;
        $postData = array_merge($this->generateRequestData(), $data);
        $postData[$fileField] = new CURLFile($filePath, mime_content_type($filePath), $fileName);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_COOKIEJAR      => $this->cookieFile,
            CURLOPT_COOKIEFILE     => $this->cookieFile,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
        ]);

        $output = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($output === false) {
            throw new ConnectionException("cURL error: {$error}");
        }

        $decoded = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ConnectionException("Invalid JSON response: " . json_last_error_msg());
        }

        return $decoded;
    }
}
