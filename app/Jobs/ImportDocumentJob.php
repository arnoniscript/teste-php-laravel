<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $documentData)
    {
        $this->documentData = $documentData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $category = Category::firstOrCreate(['name' => $this->documentData['categoria']]);

        Document::create([
            'category_id' => $category->id,
            'title' => $this->documentData['titulo'],
            'contents' => $this->documentData['conteúdo'],
        ]);
    }
}
