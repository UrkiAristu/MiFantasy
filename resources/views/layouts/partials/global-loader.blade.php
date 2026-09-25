{{-- Overlay Global de Carga y Anti-Doble Clic --}}
<div id="global-loading-overlay" class="global-loading-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Cargando">
    <div class="global-loading-card">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; border-width: 0.25rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <div class="global-loading-text mt-3 text-white fw-semibold">
            <span>Cargando</span><span class="dot-1">.</span><span class="dot-2">.</span><span class="dot-3">.</span>
        </div>
        <small class="text-white-50 mt-1">Por favor, espera un momento</small>
    </div>
</div>

<style>
    .global-loading-overlay {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }

    .global-loading-overlay.active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .global-loading-card {
        background: rgba(30, 41, 59, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 1.25rem;
        padding: 2rem 2.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        transform: scale(0.95);
        transition: transform 0.25s ease;
    }

    .global-loading-overlay.active .global-loading-card {
        transform: scale(1);
    }

    .global-loading-text .dot-1 { animation: dotPulse 1.4s infinite; animation-delay: 0.0s; }
    .global-loading-text .dot-2 { animation: dotPulse 1.4s infinite; animation-delay: 0.2s; }
    .global-loading-text .dot-3 { animation: dotPulse 1.4s infinite; animation-delay: 0.4s; }

    @keyframes dotPulse {
        0%, 80%, 100% { opacity: 0.2; }
        40% { opacity: 1; }
    }
</style>

<script>
(function() {
    'use strict';

    let activeAsyncCount = 0;
    let overlayTimer = null;
    const OVERLAY_DELAY_MS = 500;
    const overlayEl = document.getElementById('global-loading-overlay');

    function showOverlay() {
        if (overlayEl) {
            overlayEl.classList.add('active');
            overlayEl.setAttribute('aria-hidden', 'false');
        }
    }

    function hideOverlay() {
        if (overlayEl) {
            overlayEl.classList.remove('active');
            overlayEl.setAttribute('aria-hidden', 'true');
        }
    }

    function requestStarted() {
        activeAsyncCount++;
        if (activeAsyncCount === 1 && !overlayTimer) {
            overlayTimer = setTimeout(function() {
                if (activeAsyncCount > 0) {
                    showOverlay();
                }
            }, OVERLAY_DELAY_MS);
        }
    }

    function requestEnded() {
        activeAsyncCount = Math.max(0, activeAsyncCount - 1);
        if (activeAsyncCount === 0) {
            if (overlayTimer) {
                clearTimeout(overlayTimer);
                overlayTimer = null;
            }
            hideOverlay();
        }
    }

    function disableButtonWithSpinner(btn) {
        if (!btn || btn.dataset.btnLoadingActive === 'true') return;
        if (btn.hasAttribute('data-no-loading') || (btn.form && btn.form.hasAttribute('data-no-loading'))) return;

        const originalWidth = btn.offsetWidth;
        if (originalWidth > 0) {
            btn.style.minWidth = originalWidth + 'px';
        }

        btn.dataset.btnLoadingActive = 'true';
        btn.dataset.originalHtml = btn.innerHTML;
        btn.classList.add('disabled');
        btn.setAttribute('aria-disabled', 'true');

        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Cargando...';

        setTimeout(function() {
            btn.disabled = true;
        }, 0);
    }

    function resetButtonState(btn) {
        if (!btn || btn.dataset.btnLoadingActive !== 'true') return;
        if (btn.dataset.originalHtml) {
            btn.innerHTML = btn.dataset.originalHtml;
            delete btn.dataset.originalHtml;
        }
        btn.style.minWidth = '';
        btn.classList.remove('disabled');
        btn.removeAttribute('aria-disabled');
        btn.disabled = false;
        delete btn.dataset.btnLoadingActive;
    }

    function handleFormSubmission(form, submitter) {
        if (!form || form.hasAttribute('data-no-loading')) return;

        const submitBtn = submitter || form.querySelector('button[type="submit"], input[type="submit"]') || document.activeElement;
        if (submitBtn && (submitBtn.tagName === 'BUTTON' || (submitBtn.tagName === 'INPUT' && submitBtn.type === 'submit'))) {
            disableButtonWithSpinner(submitBtn);
        }

        requestStarted();

        // Limpieza de seguridad tras 15 segundos si no hubo recarga/navegación de página
        setTimeout(function() {
            if (submitBtn) resetButtonState(submitBtn);
            requestEnded();
        }, 15000);
    }

    // Interceptar envíos de formularios en fase de burbujeo para respetar preventDefault() de validaciones o SweetAlert
    document.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;

        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // Si la validación HTML5 falla, el navegador cancela el envío automáticamente
        if (form.checkValidity && !form.checkValidity()) {
            return;
        }

        handleFormSubmission(form, e.submitter);
    }, false);

    // Interceptar form.submit() programático (usado tras confirmaciones de SweetAlert, etc.)
    const originalFormSubmit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = function() {
        handleFormSubmission(this);
        return originalFormSubmit.apply(this, arguments);
    };

    // Interceptar Fetch global
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function() {
            const args = Array.prototype.slice.call(arguments);
            const options = args[1] || {};
            const isOptOut = options.skipLoading || (options.headers && options.headers['X-No-Loading']);

            if (!isOptOut) {
                requestStarted();
            }

            return originalFetch.apply(this, args)
                .then(function(response) {
                    if (!isOptOut) requestEnded();
                    return response;
                })
                .catch(function(error) {
                    if (!isOptOut) requestEnded();
                    throw error;
                });
        };
    }

    // Interceptar XMLHttpRequest global con un único listener en loadend (garantizado por estándar WHATWG)
    if (window.XMLHttpRequest) {
        const originalXhrOpen = XMLHttpRequest.prototype.open;
        const originalXhrSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.open = function() {
            this._loadingTracked = true;
            return originalXhrOpen.apply(this, arguments);
        };

        XMLHttpRequest.prototype.send = function() {
            if (this._loadingTracked) {
                requestStarted();
                const self = this;
                this.addEventListener('loadend', function() {
                    if (self._loadingTracked) {
                        self._loadingTracked = false;
                        requestEnded();
                    }
                }, { once: true });
            }
            return originalXhrSend.apply(this, arguments);
        };
    }

    // Restaurar estado al regresar vía historial / BFCache
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            activeAsyncCount = 0;
            if (overlayTimer) {
                clearTimeout(overlayTimer);
                overlayTimer = null;
            }
            hideOverlay();
            document.querySelectorAll('[data-btn-loading-active="true"]').forEach(resetButtonState);
        }
    });

    // Exponer API global
    window.MiFantasyLoader = {
        show: showOverlay,
        hide: hideOverlay,
        start: requestStarted,
        end: requestEnded,
        disableButton: disableButtonWithSpinner,
        resetButton: resetButtonState
    };
})();
</script>
