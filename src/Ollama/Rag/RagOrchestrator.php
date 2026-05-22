<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Rag;

use Imadepurnamayasa\PhpInti\Ollama\OllamaClient;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class RagOrchestrator
 *
 * Act as a Facade design pattern that orchestrates the entire Retrieval-Augmented Generation (RAG) workflow.
 * It integrates text chunking (TextSplitterInterface), vector embedding generation (EmbedderInterface),
 * and similarity retrieval (VectorStoreInterface) using Dependency Injection, providing a single, clean
 * interface for indexing and query answering.
 */
class RagOrchestrator
{
    /**
     * @var TextSplitterInterface The sliding window text chunking strategy.
     */
    private TextSplitterInterface $textSplitter;

    /**
     * @var EmbedderInterface The strategy for converting texts into vector float arrays.
     */
    private EmbedderInterface $embedder;

    /**
     * @var VectorStoreInterface The vector database strategy.
     */
    private VectorStoreInterface $vectorStore;

    /**
     * @var OllamaClient The API Client Facade.
     */
    private OllamaClient $client;

    /**
     * @var string The target LLM model name for text generation.
     */
    private string $llmModel;

    /**
     * RagOrchestrator constructor.
     *
     * @param OllamaClient $client The Ollama client for text generation.
     * @param string $llmModel The LLM model name for generation (e.g. 'llama3').
     * @param TextSplitterInterface $textSplitter The splitting strategy.
     * @param EmbedderInterface $embedder The embedding strategy.
     * @param VectorStoreInterface $vectorStore The vector database strategy.
     */
    public function __construct(
        OllamaClient $client,
        string $llmModel,
        TextSplitterInterface $textSplitter,
        EmbedderInterface $embedder,
        VectorStoreInterface $vectorStore
    ) {
        $this->client = $client;
        $this->llmModel = $llmModel;
        $this->textSplitter = $textSplitter;
        $this->embedder = $embedder;
        $this->vectorStore = $vectorStore;
    }

    /**
     * Factory method to create a RagOrchestrator with default strategies.
     *
     * @param OllamaClient $client The Ollama client.
     * @param string $llmModel The model for chat/generation (default: 'llama3').
     * @param string $embeddingModel The model for embeddings (default: 'all-minilm').
     * @param int $chunkSize Maximum size of chunks (default: 500 characters).
     * @param int $chunkOverlap Overlap characters (default: 50 characters).
     * @return self
     */
    public static function createDefault(
        OllamaClient $client,
        string $llmModel = 'llama3',
        string $embeddingModel = 'all-minilm',
        int $chunkSize = 500,
        int $chunkOverlap = 50
    ): self {
        $splitter = new CharacterTextSplitter("\n", $chunkSize, $chunkOverlap);
        $embedder = new OllamaEmbedder($client, $embeddingModel);
        $store = new InMemoryVectorStore();

        return new self($client, $llmModel, $splitter, $embedder, $store);
    }

    /**
     * Indexes raw text by splitting it into chunks, calculating embeddings, and storing them.
     *
     * @param string $text The text content.
     * @param array $metadata Custom metadata associated with this text.
     * @return void
     */
    public function indexText(string $text, array $metadata = []): void
    {
        if (empty($text)) {
            return;
        }

        // 1. Split text into chunks
        $chunks = $this->textSplitter->split($text, $metadata);

        // 2. Compute embeddings for all chunks in batch
        $this->embedder->embedDocuments($chunks);

        // 3. Persist in vector store
        $this->vectorStore->addDocuments($chunks);
    }

    /**
     * Reads a file from the filesystem and indexes its content.
     *
     * @param string $filePath Absolute path to the file.
     * @param array $metadata Custom metadata to associate.
     * @return void
     * @throws InvalidArgumentException If file does not exist or is unreadable.
     */
    public function indexFile(string $filePath, array $metadata = []): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException("File '{$filePath}' does not exist or is not readable.");
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException("Failed to read contents of '{$filePath}'.");
        }

        $baseMetadata = array_merge([
            'source_file' => basename($filePath)
        ], $metadata);

        $this->indexText($content, $baseMetadata);
    }

    /**
     * Retrieves the top K documents relevant to the given query.
     *
     * @param string $query The search query.
     * @param int $k The number of documents to retrieve.
     * @return array[] Array of arrays matching: [['document' => Document, 'score' => float]].
     */
    public function retrieveContext(string $query, int $k = 4): array
    {
        if (empty($query)) {
            return [];
        }

        // Generate embedding for the query
        $queryEmbedding = $this->embedder->embedQuery($query);

        // Search the vector store
        return $this->vectorStore->similaritySearch($queryEmbedding, $k);
    }

    /**
     * Answers a query by retrieving matching context, building an augmented prompt, and calling Ollama.
     *
     * @param string $query The user query.
     * @param string $systemPrompt Custom role or instruction for the AI assistant.
     * @param int $k The number of matching context chunks to retrieve.
     * @param array $options Additional model options (e.g., temperature).
     * @return array Returns an array with keys: 'response' (string), 'context' (array of documents used), and 'raw' (full API response).
     */
    public function answerQuery(
        string $query,
        string $systemPrompt = "You are a helpful and precise professional assistant.",
        int $k = 3,
        array $options = []
    ): array {
        // 1. Retrieve the most relevant chunks
        $matches = $this->retrieveContext($query, $k);

        // 2. Format the context blocks
        $contextTextParts = [];
        $usedDocs = [];
        foreach ($matches as $match) {
            /** @var Document $doc */
            $doc = $match['document'];
            $score = round($match['score'], 4);
            $source = $doc->getMetadata()['source_file'] ?? 'Unknown Source';
            $chunkIdx = $doc->getMetadata()['chunk_index'] ?? 0;
            
            $contextTextParts[] = "[Source: {$source} | Chunk: {$chunkIdx} | Relevance: {$score}]\n{$doc->getContent()}";
            $usedDocs[] = [
                'content' => $doc->getContent(),
                'metadata' => $doc->getMetadata(),
                'score' => $score
            ];
        }

        $contextBlock = implode("\n\n---\n\n", $contextTextParts);

        // 3. Build the Retrieval-Augmented User Prompt
        $augmentedUserMessage = <<<PROMPT
Context information is provided below. You MUST answer the query using ONLY the provided context. If the context does not contain the answer, say "I cannot find the answer in the provided documents." Do not invent or guess.

---------------------
CONTEXT:
{$contextBlock}
---------------------

QUERY:
{$query}
PROMPT;

        // 4. Send request to Ollama
        $chatBuilder = $this->client->createChatBuilder()
            ->model($this->llmModel)
            ->addSystemMessage($systemPrompt)
            ->addUserMessage($augmentedUserMessage);

        if (!empty($options)) {
            $chatBuilder->options($options);
        }

        $apiResponse = $this->client->chat($chatBuilder);
        $responseContent = $apiResponse['message']['content'] ?? '';

        return [
            'response' => $responseContent,
            'context' => $usedDocs,
            'raw' => $apiResponse
        ];
    }
}
