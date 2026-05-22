<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Rag;

/**
 * Class CharacterTextSplitter
 *
 * A concrete text splitter strategy that splits text using a separator (e.g. newline or space),
 * maintaining a specific chunk size and chunk overlap for continuity.
 */
class CharacterTextSplitter implements TextSplitterInterface
{
    /**
     * @var string The separator used to split paragraphs or lines.
     */
    private string $separator;

    /**
     * @var int The maximum character limit for each text chunk.
     */
    private int $chunkSize;

    /**
     * @var int The number of characters of overlap between consecutive chunks.
     */
    private int $chunkOverlap;

    /**
     * CharacterTextSplitter constructor.
     *
     * @param string $separator The separator to split the text by (default: newline "\n").
     * @param int $chunkSize The maximum character length of each chunk (default: 500).
     * @param int $chunkOverlap The overlapping character length between consecutive chunks (default: 50).
     */
    public function __construct(string $separator = "\n", int $chunkSize = 500, int $chunkOverlap = 50)
    {
        $this->separator = $separator;
        $this->chunkSize = $chunkSize;
        $this->chunkOverlap = $chunkOverlap;
    }

    /**
     * Splits a long text string into an array of Document objects based on the configured separator,
     * chunk size, and chunk overlap.
     *
     * @param string $text The raw text to split.
     * @param array $baseMetadata Base metadata to copy to each chunk.
     * @return Document[] Array of split Document chunks.
     */
    public function split(string $text, array $baseMetadata = []): array
    {
        $splits = explode($this->separator, $text);
        $documents = [];
        $currentChunk = [];
        $currentLength = 0;
        $chunkIndex = 0;

        foreach ($splits as $split) {
            $split = trim($split);
            if (empty($split)) {
                continue;
            }

            $splitLength = strlen($split);

            // If a single paragraph/split is larger than the chunk size on its own,
            // we split it by spaces or just treat it as its own chunk
            if ($splitLength > $this->chunkSize) {
                // If we have something in the current chunk, save it first
                if (!empty($currentChunk)) {
                    $documents[] = $this->createDocument(implode($this->separator, $currentChunk), $baseMetadata, $chunkIndex++);
                    $currentChunk = [];
                    $currentLength = 0;
                }

                // Split large paragraph by spaces
                $words = explode(' ', $split);
                $wordChunk = [];
                $wordLength = 0;
                foreach ($words as $word) {
                    $word = trim($word);
                    if (empty($word)) {
                        continue;
                    }
                    $wordLen = strlen($word) + 1; // plus space
                    if ($wordLength + $wordLen > $this->chunkSize && !empty($wordChunk)) {
                        $documents[] = $this->createDocument(implode(' ', $wordChunk), $baseMetadata, $chunkIndex++);
                        // handle overlap by taking some words from end of current wordChunk
                        $overlapWords = [];
                        $overlapLen = 0;
                        for ($i = count($wordChunk) - 1; $i >= 0; $i--) {
                            $w = $wordChunk[$i];
                            $wLen = strlen($w) + 1;
                            if ($overlapLen + $wLen > $this->chunkOverlap) {
                                break;
                            }
                            array_unshift($overlapWords, $w);
                            $overlapLen += $wLen;
                        }
                        $wordChunk = $overlapWords;
                        $wordLength = $overlapLen;
                    }
                    $wordChunk[] = $word;
                    $wordLength += $wordLen;
                }
                if (!empty($wordChunk)) {
                    $documents[] = $this->createDocument(implode(' ', $wordChunk), $baseMetadata, $chunkIndex++);
                }
                continue;
            }

            // Regular accumulation
            if ($currentLength + $splitLength > $this->chunkSize && !empty($currentChunk)) {
                $documents[] = $this->createDocument(implode($this->separator, $currentChunk), $baseMetadata, $chunkIndex++);
                
                // Keep some items for overlap
                $overlapChunk = [];
                $overlapLen = 0;
                for ($i = count($currentChunk) - 1; $i >= 0; $i--) {
                    $item = $currentChunk[$i];
                    $itemLen = strlen($item) + strlen($this->separator);
                    if ($overlapLen + $itemLen > $this->chunkOverlap) {
                        break;
                    }
                    array_unshift($overlapChunk, $item);
                    $overlapLen += $itemLen;
                }
                $currentChunk = $overlapChunk;
                $currentLength = $overlapLen;
            }

            $currentChunk[] = $split;
            $currentLength += $splitLength + strlen($this->separator);
        }

        if (!empty($currentChunk)) {
            $documents[] = $this->createDocument(implode($this->separator, $currentChunk), $baseMetadata, $chunkIndex++);
        }

        return $documents;
    }

    /**
     * Helper method to instantiate a new Document with index metadata.
     *
     * @param string $content The text content of the chunk.
     * @param array $baseMetadata The user-supplied base metadata.
     * @param int $index The sequential index of this chunk.
     * @return Document The created Document instance.
     */
    private function createDocument(string $content, array $baseMetadata, int $index): Document
    {
        $metadata = array_merge($baseMetadata, [
            'chunk_index' => $index,
            'chunk_length' => strlen($content)
        ]);

        return new Document($content, $metadata);
    }
}
