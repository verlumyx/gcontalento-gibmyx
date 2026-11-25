(function ($) {
    'use strict';

    $(function () {
        const $page = $('#categories-index-page');
        if (!$page.length) {
            return;
        }

        const categoriesApiUrl = $page.data('categories-api-url');
        const editUrlTemplate = $page.data('edit-url-template');
        const deleteApiTemplate = $page.data('delete-api-template');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        let pendingDeleteId = null;

        function renderCategories(categories) {
            const $tbody = $('#categories-table-body');
            $tbody.empty();

            if (!categories.length) {
                $tbody.append(`
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            No hay categorías registradas para mostrar.
                        </td>
                    </tr>
                `);
                return;
            }

            categories.forEach(function (category) {
                const editUrl = editUrlTemplate.replace('__ID__', category.id);

                $tbody.append(`
                    <tr>
                        <td class="text-muted">#${category.id}</td>
                        <td class="fw-semibold">${AppUI.escapeHtml(category.name)}</td>
                        <td class="text-end">
                            <a href="${editUrl}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="bi bi-pencil-square me-1"></i>Editar
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger btn-delete-category"
                                    data-id="${category.id}"
                                    data-name="${AppUI.escapeHtml(category.name)}">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        function loadCategories() {
            $('#categories-table-body').html(`
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>Cargando categorías...
                    </td>
                </tr>
            `);

            $.ajax({
                url: categoriesApiUrl,
                method: 'GET',
            }).done(function (response) {
                renderCategories(response.data || []);
            }).fail(function (xhr) {
                AppUI.handleAjaxError(xhr);
                $('#categories-table-body').html(`
                    <tr>
                        <td colspan="3" class="text-center py-4 text-danger">
                            No se pudieron cargar las categorías.
                        </td>
                    </tr>
                `);
            });
        }

        $(document).on('click', '.btn-delete-category', function () {
            pendingDeleteId = $(this).data('id');
            $('#delete-category-name').text($(this).data('name'));
            deleteModal.show();
        });

        $('#confirm-delete-category').on('click', function () {
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
                AppUI.showAlert(response.message || 'Categoría eliminada correctamente.', 'success');
                loadCategories();
            }).fail(function (xhr) {
                deleteModal.hide();
                AppUI.handleAjaxError(xhr);
            }).always(function () {
                pendingDeleteId = null;
                AppUI.setButtonLoading($btn, false);
            });
        });

        loadCategories();
    });
})(jQuery);
