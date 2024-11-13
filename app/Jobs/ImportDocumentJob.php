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
use Exception;

class ImportDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $documentData;
    protected $exercicio;
    protected $uploadIdentifier;

    public function __construct(array $documentData, int $exercicio, string $uploadIdentifier)
    {
        $this->documentData = $documentData;
        $this->exercicio = $exercicio;
        $this->uploadIdentifier = $uploadIdentifier;
    }

    public function handle()
    {
        if (!isset($this->documentData['categoria']) || !is_string($this->documentData['categoria'])) {
            throw new Exception(__('messages.invalid_categoria', ['index' => '']));
        }
        if (!isset($this->documentData['titulo']) || !is_string($this->documentData['titulo'])) {
            throw new Exception(__('messages.invalid_titulo', ['index' => '']));
        }
        if (!isset($this->documentData['conteúdo']) || !is_string($this->documentData['conteúdo'])) {
            throw new Exception(__('messages.invalid_conteudo', ['index' => '']));
        }

        $category = Category::firstOrCreate(['name' => $this->documentData['categoria']]);
        Document::create([
            'category_id' => $category->id,
            'title' => $this->documentData['titulo'],
            'contents' => $this->documentData['conteúdo'],
            'exercicio' => $this->exercicio,
        ]);
    }

    public function failed(Exception $exception)
    {
        Log::error("Job falhou com a seguinte exceção: " . $exception->getMessage());
    }
}
