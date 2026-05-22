<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Rag;

/**
 * Interface VectorStoreInterface
 *
 * Strategy pattern interface for managing document vector persistence
 * and executing semantic similarity queries.
 */
interface VectorStoreInterface
{
    /**
     * Adds an array of Documents to the vector store.
     *
     * @param Document[] $documents An array of Document instances.
     * @return void
     */
    public function addDocuments(array $documents): void;

    /**
     * Searches for the top K documents most similar to the query vector.
     *
     * @param array $queryEmbedding The float vector of the query.
     * @param int $k The number of documents to retrieve (default: 4).
     * @return array[] Array of associative arrays in the format: [['document' => Document, 'score' => float]].
     */
    public function similaritySearch(array $queryEmbedding, int $k = 4): array;
}
