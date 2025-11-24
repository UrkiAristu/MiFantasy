<footer class="bg-dark text-white text-center py-3 mt-md-5">
    <div class="container">
        <p class="mb-0">&copy; {{ date('Y') }} MiFantasy</p>
    </div>
</footer>
<script>
    function toggleMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('show');
    }

    window.addEventListener('resize', () => {
        const menu = document.getElementById('mobileMenu');
        if (window.innerWidth >= 992) {
            menu.classList.remove('show');
        }
    });
</script>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>

@stack('scripts')