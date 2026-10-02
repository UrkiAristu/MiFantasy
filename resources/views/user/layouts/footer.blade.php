<!-- Footer de Usuario -->
<footer class="mt-auto border-t border-zinc-800/80 bg-zinc-950/60 py-6 text-center text-xs text-zinc-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p>&copy; {{ date('Y') }} MiFantasy. Plataforma de Ligas Fantasy.</p>
        <div class="flex items-center gap-4 text-zinc-400">
            <a href="{{ url('/') }}" class="hover:text-zinc-200 transition-colors">Inicio</a>
            <a href="{{ url('/user/liguillas') }}" class="hover:text-zinc-200 transition-colors">Mis Ligas</a>
            <a href="{{ url('/user/perfil') }}" class="hover:text-zinc-200 transition-colors">Perfil</a>
        </div>
    </div>
</footer>

{{-- SweetAlert2 cargado al final del DOM antes de los scripts de vista --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
