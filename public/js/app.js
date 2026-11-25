(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    });

    window.AppUI = {
        escapeHtml: function (value) {
            if (value === null || value === undefined) {
                return '';
            }
            return $('<div>').text(String(value)).html();
        },

        showAlert: function (message, type = 'success', containerSelector = '#global-alert') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
                    ${this.escapeHtml(message)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            `;
            $(containerSelector).html(alertHtml);
        },

        clearValidationErrors: function ($form) {
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');
        },

        handleAjaxError: function (xhr, $form = null) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors && $form) {
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(function (field) {
                    const $input = $form.find(`[name="${field}"]`);
                    $input.addClass('is-invalid');
                    $form.find(`[data-error-for="${field}"]`).text(errors[field][0]);
                });
                this.showAlert('Por favor corrige los errores del formulario.', 'danger');
                return;
            }

            const message = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : 'Ocurrió un error inesperado al procesar la solicitud.';
            this.showAlert(message, 'danger');
        },

        setButtonLoading: function ($button, isLoading, loadingText = 'Guardando...') {
            if (isLoading) {
                $button.data('original-html', $button.html());
                $button.prop('disabled', true).html(
                    `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>${this.escapeHtml(loadingText)}`
                );
            } else {
                $button.prop('disabled', false).html($button.data('original-html'));
            }
        },

        flashAndRedirect: function (message, url) {
            sessionStorage.setItem('flash_message', message);
            window.location.href = url;
        },
    };

    $(function () {
        const flashMessage = sessionStorage.getItem('flash_message');
        if (flashMessage) {
            sessionStorage.removeItem('flash_message');
            window.AppUI.showAlert(flashMessage, 'success');
        }
    });
})(jQuery);
