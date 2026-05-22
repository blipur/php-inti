<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Rag;

/**
 * Interface EmbedderInterface
 *
 * Strategy pattern interface for generating vector embeddings from text strings or Document objects.
 */
interface EmbedderInterface
{
    /**
     * Generates a vector embedding for a single Document and sets it internally.
     *
     * @param Document $document The Document chunk.
     * @return array The float vector embedding.
     */
    public function embedDocument(Document $document): array;

    /**
     * Generates vector embeddings for a collection of Documents and sets them internally.
     *
     * @param Document[] $documents List of Document chunks.
     * @return Document[] The updated Documents containing embeddings.
     */
    public function embedDocuments(array $documents): array;

    /**
     * Generates a vector embedding for a text query.
     *
     * @param string $query The search query.
     * @return array The float vector embedding.
     */
    public function embedQuery(string $query): array;
}
