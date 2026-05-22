<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Rag;

/**
 * Interface TextSplitterInterface
 *
 * Strategy pattern interface for splitting long source text into smaller chunks
 * that can be easily embedded and consumed by LLMs.
 */
interface TextSplitterInterface
{
    /**
     * Splits a long text string into an array of Document objects.
     *
     * @param string $text The raw text content to split.
     * @param array $baseMetadata Base metadata to apply to each resulting Document chunk.
     * @return Document[] Array of Document chunks.
     */
    public function split(string $text, array $baseMetadata = []): array;
}
