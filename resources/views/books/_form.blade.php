<div class="mb-3">
    <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="title" name="title" minlength="3" maxlength="255" required>
    <div class="invalid-feedback" data-error-for="title"></div>
</div>

<div class="mb-3">
    <label for="author" class="form-label">Autor <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="author" name="author" maxlength="255" required>
    <div class="invalid-feedback" data-error-for="author"></div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="published_year" class="form-label">Año de publicación <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="published_year" name="published_year" min="1000" max="{{ date('Y') }}" required>
        <div class="invalid-feedback" data-error-for="published_year"></div>
    </div>

    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
        <select class="form-select" id="category_id" name="category_id" required>
            <option value="">Seleccione una categoría...</option>
        </select>
        <div class="invalid-feedback" data-error-for="category_id"></div>
    </div>
</div>

<div class="mb-4">
    <label for="description" class="form-label">Descripción <span class="text-muted small">(opcional)</span></label>
    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    <div class="invalid-feedback" data-error-for="description"></div>
</div>
