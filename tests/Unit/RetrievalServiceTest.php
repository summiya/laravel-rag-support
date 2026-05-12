<?php

use App\Services\RAG\EmbeddingService;
use App\Services\RAG\RetrievalService;

it('prefers chunks matching the explicit topic in the question', function () {
    $service = new RetrievalService(Mockery::mock(EmbeddingService::class));

    $chunks = [
        [
            'id' => 5,
            'content' => 'shoes return policy',
            'similarity' => 0.27,
        ],
        [
            'id' => 1,
            'content' => 'Our return policy allows returns within 30 days. Shoes may be returned within 7 days if unworn.',
            'similarity' => 0.42,
        ],
        [
            'id' => 9,
            'content' => 'clothes in 5 days',
            'similarity' => 0.46,
        ],
    ];

    $results = $service->filterRelevantChunks($chunks, 'I am asking about clothes return policy?');

    expect($results)->toHaveCount(1)
        ->and($results[0]['id'])->toBe(9);
});

it('keeps vector results when no explicit topic is present', function () {
    $service = new RetrievalService(Mockery::mock(EmbeddingService::class));

    $chunks = [
        [
            'id' => 1,
            'content' => 'Our return policy allows returns within 30 days.',
            'similarity' => 0.30,
        ],
        [
            'id' => 2,
            'content' => 'Privacy Policy',
            'similarity' => 0.90,
        ],
    ];

    $results = $service->filterRelevantChunks($chunks, 'What is the return policy?');

    expect($results)->toHaveCount(1)
        ->and($results[0]['id'])->toBe(1);
});
