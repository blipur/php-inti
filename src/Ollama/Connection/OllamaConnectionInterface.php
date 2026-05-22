<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Connection;

/**
 * Interface OllamaConnectionInterface
 * 
 * Defines the contract for sending HTTP requests to the Ollama server.
 * Implementing this interface allows swapping transport layers (e.g. cURL, Guzzle, or mocks).
 */
interface OllamaConnectionInterface
{
    /**
     * Sends an HTTP request to the Ollama API endpoint.
     *
     * @param string $method The HTTP method (GET, POST, DELETE, etc.).
     * @param string $endpoint The API endpoint (e.g. '/api/generate', '/api/tags').
     * @param array $payload The request payload to be sent as JSON.
     * @return array The decoded JSON response from the server.
     * @throws \RuntimeException If the HTTP request fails or responds with an error code.
     */
    public function request(string $method, string $endpoint, array $payload = []): array;
}
