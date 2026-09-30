@extends('layouts.base')

@section('head')
    <title>MiFantasy — Software SaaS para Ligas y Torneos Amateur</title>
    <meta name="description" content="Lleva tu liga amateur al nivel profesional. Gestión integral multi-tenant, alineaciones en tiempo real y puntuaciones automáticas para Fútbol 11, Fútbol 7 y Fútbol Sala.">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        lime: {
                            400: '#a3e635',
                            300: '#bef264',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #09090b;
        }
    </style>
@endsection

@section('body_class', 'bg-zinc-950 text-zinc-100 antialiased selection:bg-lime-400 selection:text-zinc-950')

@section('body')
<div class="min-h-screen bg-zinc-950 text-zinc-100 flex flex-col">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 w-full border-b border-zinc-800/50 bg-zinc-950/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center transition-all duration-300 ease-out group-hover:border-zinc-700">
                    <svg class="w-5 h-5 text-lime-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-zinc-100">MiFantasy</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-400">
                <a href="#features" class="transition-colors duration-200 hover:text-zinc-100">Características</a>
                <a href="#modalidades" class="transition-colors duration-200 hover:text-zinc-100">Modalidades</a>
                <a href="#pricing" class="transition-colors duration-200 hover:text-zinc-100">Precios</a>
                <a href="#faq" class="transition-colors duration-200 hover:text-zinc-100">Preguntas Frecuentes</a>
            </nav>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-zinc-200 bg-zinc-900 border border-zinc-800 rounded-lg transition-all duration-300 ease-out hover:border-zinc-700 hover:text-zinc-100">
                        Mi Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-zinc-400 transition-colors duration-200 hover:text-zinc-100 px-3 py-2">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-zinc-950 bg-lime-400 rounded-lg transition-all duration-300 ease-out hover:bg-lime-300 hover:-translate-y-0.5">
                        Crear liga
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1">
        <!-- Hero Section -->
        <section class="relative py-24 md:py-32 overflow-hidden border-b border-zinc-900">
            <div class="max-w-5xl mx-auto px-6 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-900/80 border border-zinc-800/80 text-xs font-medium text-zinc-400 mb-8">
                    <span class="w-2 h-2 rounded-full bg-lime-400 animate-pulse"></span>
                    SaaS B2B Multi-tenant para Organizadores de Fútbol
                </div>

                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold tracking-tighter text-zinc-100 leading-[1.05] mb-8">
                    Lleva tu liga amateur <br class="hidden sm:inline">
                    al <span class="text-zinc-400">nivel profesional</span>.
                </h1>

                <p class="max-w-2xl mx-auto text-lg md:text-xl text-zinc-400 font-normal leading-relaxed mb-12">
                    Gestiona torneos, plantillas, alineaciones interactivas y estadísticas en tiempo real. Aprovisionamiento instantáneo de tu espacio exclusivo.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-zinc-950 bg-lime-400 rounded-xl transition-all duration-300 ease-out hover:bg-lime-300 hover:-translate-y-1">
                        Crear mi liga
                        <svg class="ml-2 w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="#pricing" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-medium text-zinc-300 bg-zinc-900/80 border border-zinc-800 rounded-xl transition-all duration-300 ease-out hover:border-zinc-700 hover:text-zinc-100 hover:-translate-y-1">
                        Ver planes y precios
                    </a>
                </div>

                <!-- Metrics Strip -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-20 mt-20 border-t border-zinc-900">
                    <div>
                        <div class="text-3xl font-extrabold tracking-tight text-zinc-100">F11 · F7 · Sala</div>
                        <div class="text-xs uppercase tracking-wider font-semibold text-zinc-500 mt-1">Modalidades nativas</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold tracking-tight text-zinc-100">100%</div>
                        <div class="text-xs uppercase tracking-wider font-semibold text-zinc-500 mt-1">Multi-tenant aislado</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold tracking-tight text-zinc-100">&lt; 1s</div>
                        <div class="text-xs uppercase tracking-wider font-semibold text-zinc-500 mt-1">Cálculo de puntuación</div>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold tracking-tight text-zinc-100">Bizum + Tarjeta</div>
                        <div class="text-xs uppercase tracking-wider font-semibold text-zinc-500 mt-1">Cobro automático</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bento Box Features Section -->
        <section id="features" class="py-24 md:py-32 border-b border-zinc-900">
            <div class="max-w-7xl mx-auto px-6">
                <div class="max-w-2xl mb-16">
                    <h2 class="text-3xl md:text-5xl font-extrabold tracking-tighter text-zinc-100 mb-4">
                        Diseñado para la máxima precisión organizativa.
                    </h2>
                    <p class="text-base md:text-lg text-zinc-400">
                        Cada detalle ha sido optimizado para erradicar hojas de cálculo y automatizar tu competición de principio a fin.
                    </p>
                </div>

                <!-- Asymmetric Bento Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Feature 1: Large Span (2 Columns) -->
                    <div class="md:col-span-2 bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-8 lg:p-10 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mb-6 text-zinc-100">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                    <line x1="9" y1="21" x2="9" y2="9"></line>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-3">
                                Motor de Alineaciones Interactivas
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm md:text-base mb-8">
                                Sistema visual de confección de once titular y banquillo adaptado a las tácticas de Fútbol 11, Fútbol 7 y Fútbol Sala. Restricciones por presupuesto, capitanes y posiciones automáticas.
                            </p>
                        </div>
                        <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-xl p-4 font-mono text-xs text-zinc-400 flex items-center justify-between">
                            <span class="text-zinc-300">Tácticas soportadas: 4-3-3 · 4-4-2 · 3-5-2 · 3-2-1 · 2-2-1</span>
                            <span class="text-lime-400 font-semibold">Validado en tiempo real</span>
                        </div>
                    </div>

                    <!-- Feature 2: Single Column -->
                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-8 lg:p-10 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mb-6 text-zinc-100">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-3">
                                Facturación y Cobros
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm md:text-base">
                                Integración directa con Stripe y Bizum. Gestión de cuotas de inscripción por equipo o suscripciones de liga con conciliación automática.
                            </p>
                        </div>
                        <div class="pt-6">
                            <span class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-zinc-400">
                                SCA / PSD2 & 3D Secure
                            </span>
                        </div>
                    </div>

                    <!-- Feature 3: Single Column -->
                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-8 lg:p-10 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mb-6 text-zinc-100">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-3">
                                Multi-Tenant & RBAC
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm md:text-base">
                                Espacios completamente aislados por organización. Asignación granular de permisos para árbitros, delegados y administradores de sede.
                            </p>
                        </div>
                        <div class="pt-6">
                            <span class="text-xs font-mono text-zinc-500">Spatie Teams Scoped</span>
                        </div>
                    </div>

                    <!-- Feature 4: Large Span (2 Columns) -->
                    <div class="md:col-span-2 bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-8 lg:p-10 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center mb-6 text-zinc-100">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-3">
                                Puntuaciones y Actas en Vivo
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm md:text-base mb-8">
                                Carga de actas arbitrales, goles, tarjetas y MVP con recálculo instantáneo de la clasificación general y los puntos fantasy de cada participante.
                            </p>
                        </div>
                        <div class="bg-zinc-950/80 border border-zinc-800/80 rounded-xl p-4 font-mono text-xs text-zinc-400 flex items-center justify-between">
                            <span class="text-zinc-300">Cálculo de jornadas con transacciones idempotentes</span>
                            <span class="text-zinc-500">Cero inconsistencias</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modalidades Section -->
        <section id="modalidades" class="py-24 md:py-32 border-b border-zinc-900">
            <div class="max-w-7xl mx-auto px-6">
                <div class="max-w-2xl mb-16">
                    <h2 class="text-3xl md:text-5xl font-extrabold tracking-tighter text-zinc-100 mb-4">
                        Adaptado a cualquier formato deportivo.
                    </h2>
                    <p class="text-base md:text-lg text-zinc-400">
                        Reglamentos, plantillas y sistemas de puntuación configurados específicamente para cada modalidad.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-8 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700">
                        <div class="text-xs font-mono uppercase tracking-wider text-zinc-500 mb-2">Modalidad 01</div>
                        <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-2">Fútbol 11</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Torneos estándar federados y ligas de fin de semana. Plantillas completas de 18 a 25 jugadores con esquemas tácticos clásicos.
                        </p>
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-8 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700">
                        <div class="text-xs font-mono uppercase tracking-wider text-zinc-500 mb-2">Modalidad 02</div>
                        <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-2">Fútbol 7</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            El formato rey del deporte amateur y corporativo. Rotaciones dinámicas, actas simplificadas y puntuaciones ágiles.
                        </p>
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-8 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700">
                        <div class="text-xs font-mono uppercase tracking-wider text-zinc-500 mb-2">Modalidad 03</div>
                        <h3 class="text-2xl font-bold tracking-tight text-zinc-100 mb-2">Fútbol Sala</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Ritmo frenético y alta frecuencia de eventos. Control minucioso de faltas acumuladas, asistencias y rendimiento por minuto.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 md:py-32 border-b border-zinc-900">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <div class="max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl md:text-5xl font-extrabold tracking-tighter text-zinc-100 mb-4">
                        Precios simples y transparentes.
                    </h2>
                    <p class="text-base md:text-lg text-zinc-400">
                        Sin costes ocultos por equipo ni comisiones abusivas. Todo lo que necesitas para tu temporada.
                    </p>
                </div>

                <!-- Central Pricing Card -->
                <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-3xl p-8 md:p-12 transition-all duration-300 ease-out hover:-translate-y-1 hover:border-zinc-700 text-left max-w-xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="text-xs font-mono uppercase tracking-wider text-zinc-400">Plan Básico</span>
                            <h3 class="text-2xl font-bold tracking-tight text-zinc-100">Organización de Liga</h3>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-zinc-800 text-zinc-200 border border-zinc-700">
                            Aceptamos Bizum y Tarjeta
                        </span>
                    </div>

                    <div class="flex items-baseline gap-2 mb-8">
                        <span class="text-6xl font-extrabold tracking-tighter text-lime-400">9€</span>
                        <span class="text-zinc-400 text-base font-medium">/ mes</span>
                    </div>

                    <p class="text-sm text-zinc-400 mb-8 leading-relaxed">
                        Ideal para organizadores de torneos independientes, ligas de empresas o asociaciones deportivas municipales.
                    </p>

                    <div class="space-y-4 mb-10 text-sm text-zinc-300">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-lime-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Hasta 16 equipos por competición</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-lime-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Motor fantasy con cálculo automático de jornadas</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-lime-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Gestión de actas, goleadores y sanciones</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-lime-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Portal de facturación y descargas de recibos</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-lime-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>Cancelación en 1-clic sin permanencia</span>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-zinc-950 bg-lime-400 rounded-xl transition-all duration-300 ease-out hover:bg-lime-300 hover:-translate-y-1">
                        Empezar ahora por 9€/mes
                    </a>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-24 md:py-32">
            <div class="max-w-4xl mx-auto px-6">
                <div class="max-w-2xl mb-16">
                    <h2 class="text-3xl md:text-5xl font-extrabold tracking-tighter text-zinc-100 mb-4">
                        Preguntas frecuentes
                    </h2>
                    <p class="text-base md:text-lg text-zinc-400">
                        Todo lo que necesitas saber sobre el funcionamiento de MiFantasy.
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-6 lg:p-8 transition-all duration-300 ease-out hover:border-zinc-700">
                        <h3 class="text-lg font-bold text-zinc-100 mb-2">¿Cómo funciona el pago con Bizum y tarjeta?</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Procesamos los cobros a través de Stripe Checkout en cumplimiento con la normativa PSD2. Puedes suscribirte mediante cualquier tarjeta de débito/crédito o seleccionando Bizum en la pasarela. El aprovisionamiento de tu liga es instantáneo y seguro.
                        </p>
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-6 lg:p-8 transition-all duration-300 ease-out hover:border-zinc-700">
                        <h3 class="text-lg font-bold text-zinc-100 mb-2">¿Puedo crear torneos de diferentes modalidades a la vez?</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            Sí. Desde tu panel de control como organizador puedes dar de alta competiciones simultáneas de Fútbol 11, Fútbol 7 y Fútbol Sala, cada una con sus propias reglas y plantillas.
                        </p>
                    </div>

                    <div class="bg-zinc-900/50 border border-zinc-800/50 rounded-2xl p-6 lg:p-8 transition-all duration-300 ease-out hover:border-zinc-700">
                        <h3 class="text-lg font-bold text-zinc-100 mb-2">¿Tienen permanencia las suscripciones?</h3>
                        <p class="text-sm text-zinc-400 leading-relaxed">
                            No. Puedes cancelar tu suscripción en cualquier momento con un solo clic desde el Customer Portal de Stripe integrado en tu perfil.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-900 py-12 bg-zinc-950">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold tracking-tight text-zinc-100">MiFantasy</span>
                <span class="text-zinc-600 text-sm">·</span>
                <span class="text-xs text-zinc-500">© {{ date('Y') }} Todos los derechos reservados.</span>
            </div>

            <div class="flex items-center gap-6 text-xs text-zinc-400">
                <a href="#features" class="hover:text-zinc-100 transition-colors">Características</a>
                <a href="#pricing" class="hover:text-zinc-100 transition-colors">Precios</a>
                <a href="{{ route('login') }}" class="hover:text-zinc-100 transition-colors">Acceso</a>
            </div>
        </div>
    </footer>
</div>
@endsection
