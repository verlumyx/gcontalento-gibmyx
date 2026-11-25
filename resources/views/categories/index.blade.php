@extends('layouts.app')

@section('title', 'Categorías - Biblioteca')

@section('content')
    <div id="categories-index-page"
         data-categories-api-url="{{ route('categories.index') }}"
         data-edit-url-template="{{ route('web.categories.edit', ['category' => '__ID__']) }}"
         data-delete-api-template="{{ route('categories.delete', ['category' => '__ID__']) }}">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Categorías</h1>
                <p class="text-muted mb-0">Administra las categorías del catálogo de libros.</p>
            </div>
            <a href="{{ route('web.categories.create') }}" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i>Nueva Categoría
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Nombre</th>
                            <th class="text-end" style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="categories-table-body">
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>Cargando categorías...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCategoryModalLabel">Eliminar categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que deseas eliminar la categoría <strong id="delete-category-name"></strong>? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="confirm-delete-category" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/categories/index.js') }}"></script>
@endpush
