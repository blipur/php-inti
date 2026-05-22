<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Rag;

use Imadepurnamayasa\PhpInti\Ollama\OllamaClient;
use RuntimeException;

/**
 * Class OllamaEmbedder
 *
 * Concrete implementation of EmbedderInterface using the OllamaClient to call
 * embedding API endpoints. Supports both single-item and efficient batch embeddings.
 */
class OllamaEmbedder implements EmbedderInterface
{
    /**
     * @var OllamaClient The primary Ollama client facade.
     */
    private OllamaClient $client;

    /**
     * @var string The embedding model name.
     */
    private string $model;

    /**
     * OllamaEmbedder constructor.
     *
     * @param OllamaClient $client The Ollama client instance.
     * @param string $model The embedding model name (e.g. 'all-minilm' or 'nomic-embed-text').
     */
    public function __construct(OllamaClient $client, string $model = 'all-minilm')
    {
        $this->client = $client;
        $this->model = $model;
    }

    /**
     * Generates a vector embedding for a single Document and sets it internally.
     *
     * @param Document $document The Document chunk.
     * @return array The float vector embedding.
     */
    public function embedDocument(Document $document): array
    {
        $embedding = $this->embedText($document->getContent());
        $document->setEmbedding($embedding);
        return $embedding;
    }

    /**
     * Generates vector embeddings for a collection of Documents and sets them internally.
     * Utilizes batch embedding endpoints for high efficiency.
     *
     * @param Document[] $documents List of Document chunks.
     * @return Document[] The updated Documents containing embeddings.
     */
    public function embedDocuments(array $documents): array
    {
        if (empty($documents)) {
            return [];
        }

        $inputs = array_map(fn(Document $doc) => $doc->getContent(), $documents);

        try {
            // Attempt to call `/api/embed` which supports batching
            $builder = $this->client->createEmbedBuilder()
                ->model($this->model)
                ->input($inputs);

            $response = $this->client->embed($builder);

            if (isset($response['embeddings']) && is_array($response['embeddings'])) {
                foreach ($documents as $idx => $document) {
                    if (isset($response['embeddings'][$idx])) {
                        $document->setEmbedding($response['embeddings'][$idx]);
                    }
                }
                return $documents;
            }
        } catch (\Exception $e) {
            // If the modern `/api/embed` endpoint is not available or fails,
            // fallback to calling `/api/embeddings` or `/api/embed` one-by-one.
        }

        // Fallback: Embed one-by-one
        foreach ($documents as $document) {
            $this->embedDocument($document);
        }

        return $documents;
    }

    /**
     * Generates a vector embedding for a text query.
     *
     * @param string $query The search query.
     * @return array The float vector embedding.
     */
    public function embedQuery(string $query): array
    {
        return $this->embedText($query);
    }

    /**
     * Internal helper to retrieve embedding for a single string.
     *
     * @param string $text The text string to embed.
     * @return array The generated float vector.
     * @throws RuntimeException if embedding fails.
     */
    private function embedText(string $text): array
    {
        try {
            // Try modern /api/embed first
            $builder = $this->client->createEmbedBuilder()
                ->model($this->model)
                ->input($text);

            $response = $this->client->embed($builder);

            if (isset($response['embeddings'][0])) {
                return $response['embeddings'][0];
            }
            if (isset($response['embeddings']) && !empty($response['embeddings']) && is_array($response['embeddings'][0] ?? null)) {
                return $response['embeddings'][0];
            }
        } catch (\Exception $e) {
            // Fall back to legacy /api/embeddings
        }

        // Legacy fallback
        try {
            $response = $this->client->embeddings([
                'model' => $this->model,
                'prompt' => $text,
            ]);

            if (isset($response['embedding'])) {
                return $response['embedding'];
            }
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to generate embedding for text: " . $e->getMessage(), 0, $e);
        }

        throw new RuntimeException("No embedding returned from Ollama API.");
    }
}
