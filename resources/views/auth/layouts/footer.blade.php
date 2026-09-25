<script>
    function toggleMenu() {
        const menu = document.getElementById('mobileMenu');
        if (menu) menu.classList.toggle('show');
    }

    let resizeTimerAuth;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimerAuth);
        resizeTimerAuth = setTimeout(() => {
            const menu = document.getElementById('mobileMenu');
            if (menu && window.innerWidth >= 992) {
                menu.classList.remove('show');
            }
        }, 100);
    });
</script>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
{{-- SweetAlert2 cargado al final del DOM antes de los scripts de vista --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>