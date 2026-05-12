<?php

namespace App\Services\RAG;

class DocumentChunker
{
    private int $maxChunkSize;

    public function __construct(int $maxChunkSize = 900)
    {
        $this->maxChunkSize = $maxChunkSize;
    }

    /**
     * Split the provided text into smaller chunks while preserving sentence boundaries when possible.
     */
    public function chunk(string $text): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if ($text === '') {
            return [];
        }

        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z0-9])/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (! is_array($sentences)) {
            return [$text];
        }

        $chunks = [];
        $current = '';

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);

            if ($sentence === '') {
                continue;
            }

            if ($this->fitsInChunk($current, $sentence)) {
                $current = $this->appendSentence($current, $sentence);

                continue;
            }

            if ($current !== '') {
                $chunks[] = $current;
            }

            if (mb_strlen($sentence) <= $this->maxChunkSize) {
                $current = $sentence;
            } else {
                foreach ($this->splitLongSentence($sentence) as $piece) {
                    $chunks[] = $piece;
                }

                $current = '';
            }
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    private function fitsInChunk(string $current, string $sentence): bool
    {
        if ($current === '') {
            return mb_strlen($sentence) <= $this->maxChunkSize;
        }

        return mb_strlen($current.' '.$sentence) <= $this->maxChunkSize;
    }

    private function appendSentence(string $current, string $sentence): string
    {
        return $current === '' ? $sentence : $current.' '.$sentence;
    }

    private function splitLongSentence(string $sentence): array
    {
        $words = preg_split('/\s+/u', $sentence, -1, PREG_SPLIT_NO_EMPTY);

        $chunks = [];
        $current = '';

        foreach ($words as $word) {
            if ($current === '') {
                $current = $word;

                continue;
            }

            $next = $current.' '.$word;

            if (mb_strlen($next) <= $this->maxChunkSize) {
                $current = $next;

                continue;
            }

            $chunks[] = $current;
            $current = $word;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
