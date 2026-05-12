<?php

use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\RAG\DocumentChunker;
use App\Services\RAG\EmbeddingService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rag:index-document', function () {
    $title = trim($this->ask('Document title'));
    $content = trim($this->ask('Document content'));

    if ($title === '' || $content === '') {
        $this->error('Both title and content are required.');

        return 1;
    }

    $document = Document::create([
        'title' => $title,
        'original_content' => $content,
    ]);

    $chunker = new DocumentChunker;
    $chunks = $chunker->chunk($content);

    foreach ($chunks as $index => $chunkContent) {
        $document->chunks()->create([
            'chunk_index' => $index,
            'content' => $chunkContent,
        ]);
    }

    $this->info('Document indexed successfully.');
    $this->info('Title: '.$document->title);
    $this->info('Chunks: '.count($chunks));

    return 0;
})->purpose('Index a document into the ai_pgsql RAG database');

Artisan::command('rag:generate-embeddings', function () {
    $chunks = DocumentChunk::whereNull('embedding')->get();

    if ($chunks->isEmpty()) {
        $this->info('No chunks found without embeddings.');

        return 0;
    }

    $service = new EmbeddingService;
    $progressBar = $this->output->createProgressBar($chunks->count());
    $progressBar->start();

    foreach ($chunks as $chunk) {
        try {
            $embedding = $service->generate($chunk->content);
            $chunk->update(['embedding' => $embedding]);
        } catch (Exception $e) {
            $this->error("Failed to generate embedding for chunk {$chunk->id}: {$e->getMessage()}");
        }

        $progressBar->advance();
    }

    $progressBar->finish();
    $this->newLine();
    $this->info('Embeddings generated for '.$chunks->count().' chunks.');

    return 0;
})->purpose('Generate and store embeddings for document chunks in ai_pgsql');
