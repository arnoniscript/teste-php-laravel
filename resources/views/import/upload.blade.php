@extends('layouts.app')

@section('title', 'Importar Arquivo JSON')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">

        <h5>Importar Documentos JSON</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('import.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Selecione o arquivo JSON:</label>
                <input type="file" class="form-control" name="file" id="file" required>
            </div>
            <button type="submit" class="btn btn-success">Enviar para Fila</button>
        </form>
    </div>
</div>
@endsection