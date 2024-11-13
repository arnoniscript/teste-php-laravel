@extends('layouts.app')

@section('title', 'Processamento da Fila')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5>Processamento da Fila de Importação</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('import.processQueue') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">Processar Fila</button>
        </form>
    </div>
</div>
@endsection