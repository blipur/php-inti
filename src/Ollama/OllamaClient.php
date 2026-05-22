<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama;

use Imadepurnamayasa\PhpInti\Ollama\Connection\OllamaConnectionInterface;
use Imadepurnamayasa\PhpInti\Ollama\Connection\CurlConnection;
use Imadepurnamayasa\PhpInti\Ollama\Request\GenerateRequestBuilder;
use Imadepurnamayasa\PhpInti\Ollama\Request\ChatRequestBuilder;
use Imadepurnamayasa\PhpInti\Ollama\Request\EmbedRequestBuilder;
use InvalidArgumentException;

/**
 * Class OllamaClient
 * 
 * The primary entry point and Facade to communicate with the Ollama API.
 */
class OllamaClient
{
    /**
     * @var OllamaConnectionInterface The connection adapter used for HTTP communication.
     */
    private OllamaConnectionInterface $connection;

    /**
     * OllamaClient constructor.
     *
     * @param OllamaConnectionInterface|null $connection Optional custom connection. If null, a default CurlConnection is used.
     */
    public function __construct(?OllamaConnectionInterface $connection = null)
    {
        $this->connection = $connection ?? new CurlConnection();
    }

    /**
     * Factory method to create a new client instance easily.
     *
     * @param string $baseUrl Base URL of the Ollama server.
     * @param int $timeout Maximum request timeout in seconds.
     * @return self
     */
    public static function create(string $baseUrl = 'http://localhost:11434', int $timeout = 300): self
    {
        return new self(new CurlConnection($baseUrl, $timeout));
    }

    /**
     * Factory method to get a new GenerateRequestBuilder.
     *
     * @return GenerateRequestBuilder
     */
    public function createGenerateBuilder(): GenerateRequestBuilder
    {
        return new GenerateRequestBuilder();
    }

    /**
     * Factory method to get a new ChatRequestBuilder.
     *
     * @return ChatRequestBuilder
     */
    public function createChatBuilder(): ChatRequestBuilder
    {
        return new ChatRequestBuilder();
    }

    /**
     * Factory method to get a new EmbedRequestBuilder.
     *
     * @return EmbedRequestBuilder
     */
    public function createEmbedBuilder(): EmbedRequestBuilder
    {
        return new EmbedRequestBuilder();
    }

    /**
     * Generates a completion response for a given prompt.
     *
     * @param array|GenerateRequestBuilder $request Either a pre-built GenerateRequestBuilder or raw request parameters.
     * @return array The decoded API response.
     * @throws InvalidArgumentException If request parameter type is invalid.
     */
    public function generate($request): array
    {
        if ($request instanceof GenerateRequestBuilder) {
            $payload = $request->build();
        } elseif (is_array($request)) {
            $payload = $request;
        } else {
            throw new InvalidArgumentException('Request must be an array or an instance of GenerateRequestBuilder.');
        }

        // Set stream to false to get a single complete response instead of chunked data
        if (!isset($payload['stream'])) {
            $payload['stream'] = false;
        }

        return $this->connection->request('POST', 'api/generate', $payload);
    }

    /**
     * Generates the next response in a chat sequence.
     *
     * @param array|ChatRequestBuilder $request Either a pre-built ChatRequestBuilder or raw request parameters.
     * @return array The decoded API response.
     * @throws InvalidArgumentException If request parameter type is invalid.
     */
    public function chat($request): array
    {
        if ($request instanceof ChatRequestBuilder) {
            $payload = $request->build();
        } elseif (is_array($request)) {
            $payload = $request;
        } else {
            throw new InvalidArgumentException('Request must be an array or an instance of ChatRequestBuilder.');
        }

        // Set stream to false to get a single complete response instead of chunked data
        if (!isset($payload['stream'])) {
            $payload['stream'] = false;
        }

        return $this->connection->request('POST', 'api/chat', $payload);
    }

    /**
     * Lists local models available in Ollama.
     *
     * @return array List of models returned by Ollama.
     */
    public function listModels(): array
    {
        $response = $this->connection->request('GET', 'api/tags');
        return $response['models'] ?? $response;
    }

    /**
     * Shows detailed information about a specific model.
     *
     * @param string $modelName Name of the model.
     * @return array Detailed model metadata.
     */
    public function showModel(string $modelName): array
    {
        if (empty($modelName)) {
            throw new InvalidArgumentException('Model name cannot be empty.');
        }

        return $this->connection->request('POST', 'api/show', ['name' => $modelName]);
    }

    /**
     * Generates vector embeddings for a given input using the modern `/api/embed` endpoint.
     *
     * @param array|EmbedRequestBuilder $request Either a pre-built EmbedRequestBuilder or raw request parameters.
     * @return array The decoded API response.
     * @throws InvalidArgumentException If request parameter type is invalid.
     */
    public function embed($request): array
    {
        if ($request instanceof EmbedRequestBuilder) {
            $payload = $request->build();
        } elseif (is_array($request)) {
            $payload = $request;
        } else {
            throw new InvalidArgumentException('Request must be an array or an instance of EmbedRequestBuilder.');
        }

        return $this->connection->request('POST', 'api/embed', $payload);
    }

    /**
     * Generates a vector embedding for a query using the legacy `/api/embeddings` endpoint.
     *
     * @param array $payload Raw request parameters.
     * @return array The decoded API response containing 'embedding'.
     */
    public function embeddings(array $payload): array
    {
        return $this->connection->request('POST', 'api/embeddings', $payload);
    }
}
