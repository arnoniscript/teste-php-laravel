<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportDocumentJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;




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

        $fileContent = file_get_contents($request->file('file')->getPathname());
        $data = json_decode($fileContent, true);

        $exercicio = $data['exercicio'];
        $uploadIdentifier = Str::uuid();

        session(['upload_identifier' => $uploadIdentifier]);

        foreach ($data['documentos'] as $documentData) {
            ImportDocumentJob::dispatch($documentData, $exercicio, $uploadIdentifier);
        }

        return redirect()->route('import.queue')->with('status', 'Arquivo importado para a fila com sucesso!');
    }

    public function showQueueProcessing()
    {
        return view('import.queue');
    }

    public function processQueue()
    {
        $uploadIdentifier = session('upload_identifier');

        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
        ]);

        $failedJobsCount = DB::table('failed_jobs')
            ->where('upload_identifier', $uploadIdentifier)
            ->count();

        $statusMessage = 'Fila processada com sucesso!';
        if ($failedJobsCount > 0) {
            $statusMessage .= " Há {$failedJobsCount} erro(s) e foram relacionados em failed jobs.";
        }

        return redirect()->route('import.queue')->with('status', $statusMessage);
    }

}


