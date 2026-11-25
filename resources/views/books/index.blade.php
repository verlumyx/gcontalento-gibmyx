@extends('layouts.app')

@section('title', 'Libros - Biblioteca')

@section('content')
    <div id="books-index-page"
         data-books-api-url="{{ route('books.index') }}"
         data-categories-api-url="{{ route('categories.index') }}"
         data-edit-url-template="{{ route('web.books.edit', ['book' => '__ID__']) }}"
         data-delete-api-template="{{ route('books.delete', ['book' => '__ID__']) }}">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Libros</h1>
                <p class="text-muted mb-0">Listado general del catálogo con filtro por categoría.</p>
            </div>
            <a href="{{ route('web.books.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Libro
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="category-filter" class="form-label fw-semibold mb-1">Filtrar por categoría</label>
                        <select id="category-filter" class="form-select">
                            <option value="">Todas las categorías</option>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="button" id="btn-clear-filter" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar filtro
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th style="width: 110px;">Año</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th class="text-end" style="width: 190px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="books-table-body">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>Cargando libros...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="deleteBookModal" tabindex="-1" aria-labelledby="deleteBookModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBookModalLabel">Eliminar libro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que deseas eliminar el libro <strong id="delete-book-title"></strong>? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="confirm-delete-book" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/books/index.js') }}"></script>
@endpush
