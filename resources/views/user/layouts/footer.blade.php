<script>
    function toggleMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('show');
    }
</script>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

@stack('scripts')