@extends('layouts.app')

@section('title', 'Editar Libro - Biblioteca')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Editar Libro #{{ $book->id }}</h1>
                    <p class="text-muted mb-0">Modifica los campos necesarios y guarda los cambios.</p>
                </div>
                <a href="{{ route('web.books.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver al listado
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form id="book-form"
                          data-categories-api-url="{{ route('categories.index') }}"
                          data-show-url="{{ route('books.show', ['book' => $book->id]) }}"
                          data-submit-url="{{ route('books.update', ['book' => $book->id]) }}"
                          data-submit-method="PUT"
                          data-index-url="{{ route('web.books.index') }}"
                          data-success-message="Libro actualizado correctamente."
                          data-loading-text="Actualizando..."
                          novalidate>
                        @include('books._form')

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('web.books.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" id="btn-submit-book" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Actualizar Libro
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
