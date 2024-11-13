<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportDocumentJob;

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

        foreach ($data['documentos'] as $documentData) {
            ImportDocumentJob::dispatch($documentData);
        }

        return redirect()->route('import.queue')->with('status', 'Arquivo importado para a fila com sucesso!');
    }
}
