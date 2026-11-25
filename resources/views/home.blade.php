@extends('layouts.app')

@section('title', 'Inicio - Biblioteca')

@section('content')
    <div class="p-4 p-md-5 mb-4 bg-white rounded-3 border shadow-sm">
        <div class="container-fluid py-2">
            <h1 class="display-6 fw-bold mb-2">Catálogo de Libros y Categorías</h1>
            <p class="col-lg-8 fs-5 text-muted mb-4">
                Administra libros y categorías mediante vistas Blade conectadas a la API REST usando jQuery, AJAX y Bootstrap.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('web.books.index') }}" class="btn btn-primary">
                    <i class="bi bi-journal-bookmark me-1"></i>Ver Libros
                </a>
                <a href="{{ route('web.categories.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-tags me-1"></i>Ver Categorías
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-semibold">
                        <i class="bi bi-journal-bookmark text-primary me-2"></i>Libros
                    </h5>
                    <p class="card-text text-muted">
                        Lista los libros almacenados, filtra por categoría en tiempo real, crea nuevos títulos, edita o elimina registros.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('web.books.index') }}" class="btn btn-sm btn-primary">Listar libros</a>
                        <a href="{{ route('web.books.create') }}" class="btn btn-sm btn-outline-secondary">Crear libro</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-semibold">
                        <i class="bi bi-tags text-success me-2"></i>Categorías
                    </h5>
                    <p class="card-text text-muted">
                        Consulta el catálogo de categorías disponibles, crea nuevas categorías, edita sus nombres o elimínalas.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('web.categories.index') }}" class="btn btn-sm btn-success">Listar categorías</a>
                        <a href="{{ route('web.categories.create') }}" class="btn btn-sm btn-outline-secondary">Crear categoría</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
