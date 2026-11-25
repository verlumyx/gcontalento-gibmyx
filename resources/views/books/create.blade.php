@extends('layouts.app')

@section('title', 'Crear Libro - Biblioteca')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Crear Libro</h1>
                    <p class="text-muted mb-0">Completa los datos para registrar un nuevo libro.</p>
                </div>
                <a href="{{ route('web.books.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver al listado
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form id="book-form"
                          data-categories-api-url="{{ route('categories.index') }}"
                          data-submit-url="{{ route('books.create') }}"
                          data-submit-method="POST"
                          data-index-url="{{ route('web.books.index') }}"
                          data-success-message="Libro creado correctamente."
                          data-loading-text="Guardando..."
                          novalidate>
                        @include('books._form')

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('web.books.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" id="btn-submit-book" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Guardar Libro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/books/form.js') }}"></script>
@endpush
