<?php

namespace App\Services\RAG;

use App\Models\DocumentChunk;

class RetrievalService
{
    private const CandidateLimit = 15;

    private const ResultLimit = 5;

    private const MaxSimilarityDistance = 0.75;

    private EmbeddingService $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
    }

    /**
     * Retrieve the top 5 most similar document chunks for the given question.
     */
    public function retrieve(string $question): array
    {
        $embedding = $this->embeddingService->generate($question);

        if (empty($embedding)) {
            return [];
        }

        // Convert embedding array to PostgreSQL vector string format
        $vectorString = '['.implode(',', $embedding).']';

        $chunks = DocumentChunk::selectRaw('*, (embedding <=> ?) as similarity', [$vectorString])
            ->whereNotNull('embedding')
            ->orderBy('similarity')
            ->limit(self::CandidateLimit)
            ->get()
            ->toArray();

        return $this->filterRelevantChunks($chunks, $question);
    }

    /**
     * Keep vector results focused on explicit user topics.
     *
     * @param  array<int, array<string, mixed>>  $chunks
     * @return array<int, array<string, mixed>>
     */
    public function filterRelevantChunks(array $chunks, string $question): array
    {
        $topicTerms = $this->topicTerms($question);

        if ($topicTerms !== []) {
            $topicMatches = array_values(array_filter($chunks, function (array $chunk) use ($topicTerms): bool {
                return $this->topicScore((string) ($chunk['content'] ?? ''), $topicTerms) > 0;
            }));

            if ($topicMatches !== []) {
                $chunks = $topicMatches;
            }
        }

        $chunks = array_values(array_filter($chunks, function (array $chunk): bool {
            return (float) ($chunk['similarity'] ?? 1) <= self::MaxSimilarityDistance;
        }));

        usort($chunks, function (array $first, array $second) use ($topicTerms): int {
            $firstTopicScore = $this->topicScore((string) ($first['content'] ?? ''), $topicTerms);
            $secondTopicScore = $this->topicScore((string) ($second['content'] ?? ''), $topicTerms);

            if ($firstTopicScore !== $secondTopicScore) {
                return $secondTopicScore <=> $firstTopicScore;
            }

            return (float) ($first['similarity'] ?? 1) <=> (float) ($second['similarity'] ?? 1);
        });

        return array_slice($chunks, 0, self::ResultLimit);
    }

    /**
     * Build context string from retrieved chunks.
     */
    public function buildContext(array $chunks): string
    {
        if (empty($chunks)) {
            return 'No relevant knowledge found.';
        }

        $context = '';

        foreach ($chunks as $chunk) {
            $context .= $chunk['content']."\n\n";
        }

        return trim($context);
    }

    /**
     * @return array<int, string>
     */
    private function topicTerms(string $question): array
    {
        $words = $this->normalizedWords($question);
        $stopWords = [
            'a', 'an', 'and', 'are', 'ask', 'asking', 'about', 'after', 'be', 'buy', 'buying',
            'can', 'could', 'day', 'days', 'do', 'for', 'help', 'how', 'i', 'in', 'is',
            'item', 'items', 'me', 'my', 'of', 'on', 'order', 'policy', 'purchase', 'purchased',
            'question', 'refund', 'return', 'returned', 'returns', 'the', 'this', 'to', 'what',
            'when', 'with', 'you',
        ];

        return array_values(array_unique(array_filter($words, function (string $word) use ($stopWords): bool {
            return strlen($word) > 2 && ! in_array($word, $stopWords, true);
        })));
    }

    /**
     * @param  array<int, string>  $topicTerms
     */
    private function topicScore(string $content, array $topicTerms): int
    {
        if ($topicTerms === []) {
            return 0;
        }

        $contentWords = $this->normalizedWords($content);

        return count(array_intersect($topicTerms, $contentWords));
    }

    /**
     * @return array<int, string>
     */
    private function normalizedWords(string $text): array
    {
        $words = str($text)
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->explode(' ')
            ->filter()
            ->map(function (string $word): string {
                if (strlen($word) > 3 && str($word)->endsWith('s')) {
                    return substr($word, 0, -1);
                }

                return $word;
            })
            ->values()
            ->all();

        return $words;
    }
}
