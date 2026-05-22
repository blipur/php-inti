<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Rag;

use InvalidArgumentException;

/**
 * Class InMemoryVectorStore
 *
 * A concrete Strategy implementing a memory-backed vector database.
 * Documents and their vector embeddings are stored in memory, and search queries
 * are resolved by computing the cosine similarity between the query embedding and each document.
 */
class InMemoryVectorStore implements VectorStoreInterface
{
    /**
     * @var Document[] The collection of documents in the store.
     */
    private array $documents = [];

    /**
     * Adds an array of Documents to the vector store.
     * Only stores documents that have a non-null embedding.
     *
     * @param Document[] $documents
     * @return void
     * @throws InvalidArgumentException If a document does not have an embedding.
     */
    public function addDocuments(array $documents): void
    {
        foreach ($documents as $doc) {
            if ($doc->getEmbedding() === null) {
                throw new InvalidArgumentException("Cannot add document without an embedding vector.");
            }
            $this->documents[] = $doc;
        }
    }

    /**
     * Searches for the top K documents most similar to the query vector.
     * Calculates cosine similarity for all stored documents, sorts them, and returns top K.
     *
     * @param array $queryEmbedding The float vector of the query.
     * @param int $k The number of documents to retrieve (default: 4).
     * @return array[] Array of associative arrays, e.g., [['document' => Document, 'score' => float]].
     */
    public function similaritySearch(array $queryEmbedding, int $k = 4): array
    {
        if (empty($this->documents) || empty($queryEmbedding)) {
            return [];
        }

        $results = [];

        foreach ($this->documents as $doc) {
            $docEmbedding = $doc->getEmbedding();
            if ($docEmbedding === null) {
                continue;
            }

            $score = $this->cosineSimilarity($queryEmbedding, $docEmbedding);
            $results[] = [
                'document' => $doc,
                'score' => $score
            ];
        }

        // Sort descending by similarity score
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        // Return top K
        return array_slice($results, 0, $k);
    }

    /**
     * Computes the cosine similarity between two float vectors.
     * Formula: (A . B) / (||A|| * ||B||)
     *
     * @param array $vectorA
     * @param array $vectorB
     * @return float Similarity score between -1.0 and 1.0.
     */
    private function cosineSimilarity(array $vectorA, array $vectorB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        $count = count($vectorA);
        // Ensure vector sizes match
        $limit = min($count, count($vectorB));

        for ($i = 0; $i < $limit; $i++) {
            $valA = (float)$vectorA[$i];
            $valB = (float)$vectorB[$i];

            $dotProduct += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        if ($normA === 0.0 || $normB === 0.0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
