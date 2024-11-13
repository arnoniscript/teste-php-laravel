<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $documentData;
    public $exercicio;

    public function __construct(array $documentData, int $exercicio)
    {
        $this->documentData = $documentData;
        $this->exercicio = $exercicio;
    }

    public function handle(): void
    {
        try {
            $category = Category::firstOrCreate(['name' => $this->documentData['categoria']]);

            Document::create([
                'category_id' => $category->id,
                'title' => $this->documentData['titulo'],
                'contents' => $this->documentData['conteúdo'],
                'exercicio' => $this->exercicio,
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao processar o documento: {$e->getMessage()}");
            throw $e;
        }
    }
}
