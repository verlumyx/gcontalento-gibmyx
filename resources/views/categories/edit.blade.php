@extends('layouts.app')

@section('title', 'Editar Categoría - Biblioteca')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Editar Categoría #{{ $category->id }}</h1>
                    <p class="text-muted mb-0">Actualiza el nombre de la categoría seleccionada.</p>
                </div>
                <a href="{{ route('web.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver al listado
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form id="category-form"
                          data-show-url="{{ route('categories.show', ['category' => $category->id]) }}"
                          data-submit-url="{{ route('categories.update', ['category' => $category->id]) }}"
                          data-submit-method="PUT"
                          data-index-url="{{ route('web.categories.index') }}"
                          data-success-message="Categoría actualizada correctamente."
                          data-loading-text="Actualizando..."
                          novalidate>
                        @include('categories._form')

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('web.categories.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" id="btn-submit-category" class="btn btn-success">
                                <i class="bi bi-save me-1"></i>Actualizar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/categories/form.js') }}"></script>
@endpush
