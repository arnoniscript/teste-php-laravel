<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportDocumentJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class ImportController extends Controller
{
    public function showUploadForm()
    {
        return view('import.upload');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        try {
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $data = json_decode($fileContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(__('messages.json_parse_error', ['error' => json_last_error_msg()]));
            }

            // Validações de estrutura e tipos dos dados do JSON
            $this->validateJsonStructure($data);

            $exercicio = $data['exercicio'];
            $uploadIdentifier = Str::uuid();

            foreach ($data['documentos'] as $documentData) {
                ImportDocumentJob::dispatch($documentData, $exercicio, $uploadIdentifier);
            }

            return redirect()->route('import.queue')->with('status', __('messages.upload_success'));
        } catch (Exception $e) {
            Log::error("Erro ao processar o upload: {$e->getMessage()}");
            return redirect()->route('import.upload')->withErrors($e->getMessage());
        }
    }


    public function showQueueProcessing()
    {
        return view('import.queue');
    }

    public function processQueue()
    {
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
        ]);

        $failedJobsCount = DB::table('failed_jobs')->count();

        $statusMessage = __('messages.queue_processed_success');
        if ($failedJobsCount > 0) {
            $statusMessage = __('messages.queue_processed_with_errors', ['count' => $failedJobsCount]);
        }

        return redirect()->route('import.queue')->with('status', $statusMessage);
    }

    /**
     * Valida a estrutura do JSON e tipos de dados dos campos obrigatórios.
     *
     * @param array $data
     * @throws Exception
     */
    private function validateJsonStructure(array $data): void
    {
        if (!isset($data['exercicio']) || !is_int($data['exercicio']) || strlen((string) $data['exercicio']) !== 4) {
            throw new Exception(__('messages.invalid_exercicio'));
        }

        if (!isset($data['documentos']) || !is_array($data['documentos'])) {
            throw new Exception(__('messages.invalid_documentos'));
        }

        foreach ($data['documentos'] as $index => $documentData) {
            if (!isset($documentData['categoria']) || !is_string($documentData['categoria'])) {
                throw new Exception(__('messages.invalid_categoria', ['index' => $index]));
            }
            if (!isset($documentData['titulo']) || !is_string($documentData['titulo'])) {
                throw new Exception(__('messages.invalid_titulo', ['index' => $index]));
            }
            if (!isset($documentData['conteúdo']) || !is_string($documentData['conteúdo'])) {
                throw new Exception(__('messages.invalid_conteudo', ['index' => $index]));
            }
        }
    }
}
