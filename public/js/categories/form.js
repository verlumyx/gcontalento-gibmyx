(function ($) {
    'use strict';

    $(function () {
        const $form = $('#category-form');
        if (!$form.length) {
            return;
        }

        const showApiUrl = $form.data('show-url');
        const submitApiUrl = $form.data('submit-url');
        const submitMethod = $form.data('submit-method') || 'POST';
        const indexWebUrl = $form.data('index-url');
        const successMessage = $form.data('success-message');
        const loadingText = $form.data('loading-text') || 'Guardando...';
        const $submitBtn = $('#btn-submit-category');

        if (showApiUrl) {
            $.ajax({
                url: showApiUrl,
                method: 'GET',
            }).done(function (response) {
                const category = response.data || {};
                $('#name').val(category.name || '');
            }).fail(function (xhr) {
                AppUI.handleAjaxError(xhr);
            });
        }

        $form.on('submit', function (e) {
            e.preventDefault();

            AppUI.clearValidationErrors($form);
            AppUI.setButtonLoading($submitBtn, true, loadingText);

            $.ajax({
                url: submitApiUrl,
                method: submitMethod,
                data: {
                    name: $('#name').val().trim(),
                },
            }).done(function () {
                AppUI.flashAndRedirect(successMessage, indexWebUrl);
            }).fail(function (xhr) {
                AppUI.handleAjaxError(xhr, $form);
                AppUI.setButtonLoading($submitBtn, false);
            });
        });
    });
})(jQuery);
