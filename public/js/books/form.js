(function ($) {
    'use strict';

    $(function () {
        const $form = $('#book-form');
        if (!$form.length) {
            return;
        }

        const categoriesApiUrl = $form.data('categories-api-url');
        const showBookApiUrl = $form.data('show-url');
        const submitApiUrl = $form.data('submit-url');
        const submitMethod = $form.data('submit-method') || 'POST';
        const indexWebUrl = $form.data('index-url');
        const successMessage = $form.data('success-message');
        const loadingText = $form.data('loading-text') || 'Guardando...';
        const $submitBtn = $('#btn-submit-book');

        function populateCategories(categories) {
            const $select = $('#category_id');
            (categories || []).forEach(function (category) {
                $select.append(
                    `<option value="${category.id}">${AppUI.escapeHtml(category.name)}</option>`
                );
            });
        }

        if (showBookApiUrl) {
            $.when(
                $.ajax({ url: categoriesApiUrl, method: 'GET' }),
                $.ajax({ url: showBookApiUrl, method: 'GET' })
            ).done(function (categoriesRes, bookRes) {
                populateCategories(categoriesRes[0].data);
                const book = bookRes[0].data || {};
                $('#title').val(book.title || '');
                $('#author').val(book.author || '');
                $('#published_year').val(book.published_year || '');
                $('#category_id').val(book.category_id || '');
                $('#description').val(book.description || '');
            }).fail(function (xhr) {
                AppUI.handleAjaxError(xhr);
            });
        } else {
            $.ajax({
                url: categoriesApiUrl,
                method: 'GET',
            }).done(function (response) {
                populateCategories(response.data);
            }).fail(function (xhr) {
                AppUI.handleAjaxError(xhr);
            });
        }

        $form.on('submit', function (e) {
            e.preventDefault();

            AppUI.clearValidationErrors($form);
            AppUI.setButtonLoading($submitBtn, true, loadingText);

            const payload = {
                title: $('#title').val().trim(),
                author: $('#author').val().trim(),
                published_year: $('#published_year').val(),
                category_id: $('#category_id').val(),
                description: $('#description').val().trim() || null,
            };

            $.ajax({
                url: submitApiUrl,
                method: submitMethod,
                data: payload,
            }).done(function () {
                AppUI.flashAndRedirect(successMessage, indexWebUrl);
            }).fail(function (xhr) {
                AppUI.handleAjaxError(xhr, $form);
                AppUI.setButtonLoading($submitBtn, false);
            });
        });
    });
})(jQuery);
