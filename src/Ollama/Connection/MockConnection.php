<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Connection;

/**
 * Class MockConnection
 * 
 * Mock connection adapter for testing Ollama operations without actual network requests.
 */
class MockConnection implements OllamaConnectionInterface
{
    /**
     * @var array The list of mock responses keyed by endpoint or custom logic.
     */
    private array $mockResponses = [];

    /**
     * @var array The history of requests sent through this connection.
     */
    private array $requestHistory = [];

    /**
     * Adds a simulated response for a specific endpoint.
     *
     * @param string $endpoint The API endpoint (e.g. '/api/generate').
     * @param array $response The array representing the response payload.
     * @return void
     */
    public function setResponse(string $endpoint, array $response): void
    {
        $this->mockResponses[ltrim($endpoint, '/')] = $response;
    }

    /**
     * Retrieves the history of all requests sent through this connection.
     *
     * @return array
     */
    public function getHistory(): array
    {
        return $this->requestHistory;
    }

    /**
     * Clear request history and mock responses.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->mockResponses = [];
        $this->requestHistory = [];
    }

    /**
     * Simulates sending a request to the Ollama server.
     *
     * @param string $method The HTTP method.
     * @param string $endpoint The endpoint.
     * @param array $payload The request payload.
     * @return array The mock response data.
     */
    public function request(string $method, string $endpoint, array $payload = []): array
    {
        $cleanEndpoint = ltrim($endpoint, '/');

        // Record request history for assertions
        $this->requestHistory[] = [
            'method' => $method,
            'endpoint' => $endpoint,
            'payload' => $payload,
            'timestamp' => time()
        ];

        // Return set mock response if available
        if (isset($this->mockResponses[$cleanEndpoint])) {
            return $this->mockResponses[$cleanEndpoint];
        }

        // Auto-generate generic responses if none is set
        if ($cleanEndpoint === 'api/generate') {
            $prompt = $payload['prompt'] ?? 'default prompt';
            return [
                'model' => $payload['model'] ?? 'llama3',
                'created_at' => date('c'),
                'response' => "This is a mock response from Ollama for prompt: \"{$prompt}\"",
                'done' => true,
                'context' => [1, 2, 3],
                'total_duration' => 123456789,
                'load_duration' => 12345,
                'prompt_eval_count' => 10,
                'prompt_eval_duration' => 1234,
                'eval_count' => 20,
                'eval_duration' => 5678
            ];
        }

        if ($cleanEndpoint === 'api/chat') {
            $messages = $payload['messages'] ?? [];
            $lastMessage = !empty($messages) ? end($messages)['content'] : '';
            return [
                'model' => $payload['model'] ?? 'llama3',
                'created_at' => date('c'),
                'message' => [
                    'role' => 'assistant',
                    'content' => "This is a mock assistant reply for: \"{$lastMessage}\""
                ],
                'done' => true
            ];
        }

        if ($cleanEndpoint === 'api/tags') {
            return [
                'models' => [
                    [
                        'name' => 'llama3:latest',
                        'modified_at' => date('c'),
                        'size' => 4700000000,
                        'digest' => 'sha256:1234567890abcdef',
                        'details' => [
                            'format' => 'gguf',
                            'family' => 'llama',
                            'families' => ['llama'],
                            'parameter_size' => '8B',
                            'quantization_level' => 'Q4_K_M'
                        ]
                    ],
                    [
                        'name' => 'mistral:latest',
                        'modified_at' => date('c'),
                        'size' => 4100000000,
                        'digest' => 'sha256:abcdef1234567890',
                        'details' => [
                            'format' => 'gguf',
                            'family' => 'mistral',
                            'families' => ['mistral'],
                            'parameter_size' => '7B',
                            'quantization_level' => 'Q4_K_M'
                        ]
                    ]
                ]
            ];
        }

        return [
            'status' => 'success',
            'mocked' => true,
            'endpoint' => $endpoint
        ];
    }
}
