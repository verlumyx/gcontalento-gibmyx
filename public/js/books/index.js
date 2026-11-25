(function ($) {
    'use strict';

    $(function () {
        const $page = $('#books-index-page');
        if (!$page.length) {
            return;
        }

        const booksApiUrl = $page.data('books-api-url');
        const categoriesApiUrl = $page.data('categories-api-url');
        const editUrlTemplate = $page.data('edit-url-template');
        const deleteApiTemplate = $page.data('delete-api-template');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteBookModal'));
        let pendingDeleteId = null;

        function loadCategories() {
            return $.ajax({
                url: categoriesApiUrl,
                method: 'GET',
            }).done(function (response) {
                const $select = $('#category-filter');
                const categories = response.data || [];
                categories.forEach(function (category) {
                    $select.append(
                        `<option value="${category.id}">${AppUI.escapeHtml(category.name)}</option>`
                    );
                });
            });
        }

        function renderBooks(books) {
            const $tbody = $('#books-table-body');
            $tbody.empty();

            if (!books.length) {
                $tbody.append(`
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No hay libros registrados para mostrar.
                        </td>
                    </tr>
                `);
                return;
            }

            books.forEach(function (book) {
                const categoryName = book.category ? book.category.name : 'Sin categoría';
                const description = book.description
                    ? AppUI.escapeHtml(book.description)
                    : '<span class="text-muted fst-italic">Sin descripción</span>';
                const editUrl = editUrlTemplate.replace('__ID__', book.id);

                $tbody.append(`
                    <tr>
                        <td class="text-muted">#${book.id}</td>
                        <td class="fw-semibold">${AppUI.escapeHtml(book.title)}</td>
                        <td>${AppUI.escapeHtml(book.author)}</td>
                        <td>${AppUI.escapeHtml(book.published_year)}</td>
                        <td><span class="badge text-bg-primary">${AppUI.escapeHtml(categoryName)}</span></td>
                        <td class="text-truncate" style="max-width: 240px;">${description}</td>
                        <td class="text-end">
                            <a href="${editUrl}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="bi bi-pencil-square me-1"></i>Editar
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger btn-delete-book"
                                    data-id="${book.id}"
                                    data-title="${AppUI.escapeHtml(book.title)}">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function loadBooks(categoryId = '') {
            const params = categoryId ? { category: categoryId } : {};

            $('#books-table-body').html(`
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>Cargando libros...
                    </td>
                </tr>
            `);

            $.ajax({
                url: booksApiUrl,
                method: 'GET',
                data: params,
            }).done(function (response) {
                renderBooks(response.data || []);
            }).fail(function (xhr) {
                AppUI.handleAjaxError(xhr);
                $('#books-table-body').html(`
                    <tr>
                        <td colspan="7" class="text-center py-4 text-danger">
                            No se pudieron cargar los libros.
                        </td>
                    </tr>
                `);
            });
        }

        $('#category-filter').on('change', function () {
            loadBooks($(this).val());
        });

        $('#btn-clear-filter').on('click', function () {
            $('#category-filter').val('');
            loadBooks('');
        });

        $(document).on('click', '.btn-delete-book', function () {
            pendingDeleteId = $(this).data('id');
            $('#delete-book-title').text($(this).data('title'));
            deleteModal.show();
        });

        $('#confirm-delete-book').on('click', function () {
            if (!pendingDeleteId) {
                return;
            }

            const $btn = $(this);
            AppUI.setButtonLoading($btn, true, 'Eliminando...');

            $.ajax({
                url: deleteApiTemplate.replace('__ID__', pendingDeleteId),
                method: 'DELETE',
            }).done(function (response) {
                deleteModal.hide();
                AppUI.showAlert(response.message || 'Libro eliminado correctamente.', 'success');
                loadBooks($('#category-filter').val());
            }).fail(function (xhr) {
                deleteModal.hide();
                AppUI.handleAjaxError(xhr);
            }).always(function () {
                pendingDeleteId = null;
                AppUI.setButtonLoading($btn, false);
            });
        });

        loadCategories().always(function () {
            loadBooks('');
        });
    });
})(jQuery);
