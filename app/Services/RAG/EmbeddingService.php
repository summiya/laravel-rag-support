<?php

namespace App\Services\RAG;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    private string $model = 'all-minilm';

    private string $endpoint = 'http://localhost:11434/api/embed';

    /**
     * Generate an embedding vector for the given text.
     */
    public function generate(string $text): array
    {
        $response = Http::timeout(120)->post($this->endpoint, [
            'model' => $this->model,
            'input' => $text,
        ]);

        if (! $response->successful()) {
            throw new \Exception('Failed to generate embedding: '.$response->body());
        }

        $data = $response->json();

        return $data['embeddings'][0] ?? [];
    }
}
