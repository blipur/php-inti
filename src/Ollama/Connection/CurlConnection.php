<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Connection;

use RuntimeException;

/**
 * Class CurlConnection
 * 
 * Concrete implementation of OllamaConnectionInterface using the PHP cURL extension.
 */
class CurlConnection implements OllamaConnectionInterface
{
    /**
     * @var string The base URL of the Ollama server.
     */
    private string $baseUrl;

    /**
     * @var int Connection timeout in seconds.
     */
    private int $timeout;

    /**
     * CurlConnection constructor.
     *
     * @param string $baseUrl The base URL of the Ollama API (default: http://localhost:11434).
     * @param int $timeout The maximum execution time in seconds (default: 300).
     */
    public function __construct(string $baseUrl = 'http://localhost:11434', int $timeout = 300)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    /**
     * Sends an HTTP request to the Ollama API endpoint using cURL.
     *
     * @param string $method The HTTP method (GET, POST, DELETE, etc.).
     * @param string $endpoint The API endpoint (e.g. '/api/generate', '/api/tags').
     * @param array $payload The request payload to be sent as JSON.
     * @return array The decoded JSON response from the server.
     * @throws RuntimeException If the HTTP request fails, or returns an error.
     */
    public function request(string $method, string $endpoint, array $payload = []): array
    {
        if (!extension_loaded('curl')) {
            throw new RuntimeException('The cURL extension is required to connect to Ollama.');
        }

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if (!empty($payload)) {
            $jsonData = json_encode($payload);
            if ($jsonData === false) {
                throw new RuntimeException('Failed to encode request payload to JSON: ' . json_last_error_msg());
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            $headers[] = 'Content-Length: ' . strlen($jsonData);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException("cURL request to Ollama failed: {$curlError}");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException("Ollama API returned HTTP status {$httpCode}: {$response}");
        }

        // Parse response. Some Ollama endpoints (e.g. list models) return single JSON object.
        // If the endpoint is streaming (non-empty body of newline-delimited JSON), we might need to handle it.
        // However, in our default non-streaming mode (stream => false), Ollama returns a single JSON object.
        // We will decode the full string. If the response consists of multiple NDJSON lines, we parse the last or combine.
        // For simplicity and robust default behavior, we try standard JSON decode first.
        $data = json_decode($response, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            // Check if response is newline-delimited JSON (streaming output where stream was not disabled)
            $lines = explode("\n", trim($response));
            $decodedLines = [];
            foreach ($lines as $line) {
                if (empty($line)) {
                    continue;
                }
                $decodedLine = json_decode($line, true);
                if ($decodedLine !== null) {
                    $decodedLines[] = $decodedLine;
                }
            }
            if (!empty($decodedLines)) {
                return $decodedLines;
            }

            throw new RuntimeException('Failed to decode JSON response from Ollama: ' . json_last_error_msg() . ' Raw response: ' . $response);
        }

        return $data;
    }
}
