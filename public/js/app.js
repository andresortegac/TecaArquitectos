console.log("App TECAARQUITECTOS cargada OK");

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-live-filter]').forEach((form) => {
        let timeout = null;
        let isComposing = false;

        const submitFilter = () => {
            const params = new URLSearchParams();

            new FormData(form).forEach((value, key) => {
                const cleanValue = String(value).trim();

                if (cleanValue !== '') {
                    params.set(key, cleanValue);
                }
            });

            params.delete('page');

            const action = form.getAttribute('action') || window.location.pathname;
            const url = new URL(action, window.location.origin);
            url.search = params.toString();

            window.location.href = url.toString();
        };

        const queueSubmit = () => {
            window.clearTimeout(timeout);
            timeout = window.setTimeout(submitFilter, 350);
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            window.clearTimeout(timeout);
            submitFilter();
        });

        form.querySelectorAll('input[type="search"], input[type="text"]').forEach((input) => {
            input.addEventListener('compositionstart', () => {
                isComposing = true;
            });

            input.addEventListener('compositionend', () => {
                isComposing = false;
                queueSubmit();
            });

            input.addEventListener('input', () => {
                if (!isComposing) {
                    queueSubmit();
                }
            });
        });

        form.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', submitFilter);
        });
    });
});
