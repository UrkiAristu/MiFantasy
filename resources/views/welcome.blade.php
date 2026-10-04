@extends('layouts.base')

@section('head')
    <title>MiFantasy — El Software SaaS Definitivo para Ligas y Torneos Amateur</title>
    <meta name="description" content="Gestiona tu liga amateur con tecnología de primer nivel. Motor fantasy en tiempo real, alineaciones tácticas interactivas, cobros automatizados con Bizum y Stripe, y actas arbitrales instantáneas.">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,700&display=swap" rel="stylesheet">

    <!-- GSAP & ScrollTrigger CDN for silky-smooth physical reveals -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --brand-lime: #a3e635;
            --brand-lime-hover: #bef264;
            --brand-glow: rgba(163, 230, 53, 0.25);
        }

        /* Glassmorphism & High-precision shadows */
        .glass-panel {
            background: linear-gradient(135deg, rgba(24, 24, 27, 0.7) 0%, rgba(9, 9, 11, 0.8) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        .glass-panel-glow {
            background: linear-gradient(135deg, rgba(28, 28, 35, 0.75) 0%, rgba(12, 12, 15, 0.9) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(163, 230, 53, 0.18);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.7), 0 0 25px -5px var(--brand-glow);
        }

        .glass-panel:hover {
            border-color: rgba(255, 255, 255, 0.16);
        }

        /* Field Grass Pattern */
        .soccer-pitch {
            background-color: #0b1510;
            background-image:
                radial-gradient(ellipse at 50% 50%, rgba(20, 48, 27, 0.8) 0%, rgba(8, 20, 12, 0.98) 100%),
                repeating-linear-gradient(0deg, transparent, transparent 38px, rgba(255, 255, 255, 0.015) 38px, rgba(255, 255, 255, 0.015) 76px);
        }

        /* Ambient Lighting Mesh */
        .hero-glow-sphere {
            background: radial-gradient(circle, rgba(163, 230, 53, 0.14) 0%, rgba(163, 230, 53, 0.02) 45%, transparent 70%);
            filter: blur(50px);
        }

        .hero-glow-emerald {
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 65%);
            filter: blur(60px);
        }

        /* Smooth marquee animation */
        @keyframes scroll-x {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .animate-marquee {
            display: flex;
            width: 200%;
            animation: scroll-x 32s linear infinite;
        }

        .animate-marquee:hover {
            animation-play-state: paused;
        }

        /* Interactive pulse badge */
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .pulse-ring {
            animation: pulse-ring 2.8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Field line styling */
        .pitch-line {
            border-color: rgba(163, 230, 53, 0.28);
        }

        /* Accordion transition utility */
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.45s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.35s ease;
            opacity: 0;
        }

        .faq-answer.open {
            max-height: 280px;
            opacity: 1;
        }

        .faq-icon {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .faq-icon.rotated {
            transform: rotate(180deg);
        }
    </style>
@endsection

@section('body_class', 'bg-zinc-950 text-zinc-100 font-sans antialiased selection:bg-lime-400 selection:text-zinc-950 overflow-x-hidden')

@section('body')
<div class="relative min-h-screen bg-zinc-950 flex flex-col justify-between overflow-hidden selection:bg-lime-400 selection:text-zinc-950">

    <!-- Subtle Background Noise Grid Overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-[0.035] bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:24px_24px] z-0"></div>

    <!-- Glowing Dynamic Spotlights -->
    <div class="fixed -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[500px] hero-glow-sphere pointer-events-none z-0"></div>
    <div class="fixed top-1/3 -right-40 w-[600px] h-[600px] hero-glow-emerald pointer-events-none z-0"></div>

    <!-- ================= NAVBAR ================= -->
    <header class="sticky top-0 z-50 w-full border-b border-zinc-800/60 bg-zinc-950/75 backdrop-blur-xl transition-all duration-300">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 h-20 flex items-center justify-between">

            <!-- Logo & Brand identity -->
            <a href="{{ url('/') }}" class="flex items-center gap-1.5 sm:gap-3.5 group focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-400 rounded-xl px-1">
                <div class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-zinc-700/60 flex items-center justify-center transition-all duration-300 group-hover:scale-105 group-hover:border-lime-400/50 group-hover:shadow-[0_0_20px_rgba(163,230,53,0.25)] shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-lime-400 transition-transform duration-300 group-hover:rotate-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-lime-400 border-2 border-zinc-950"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-base sm:text-xl font-extrabold tracking-tight text-white flex items-center gap-1 sm:gap-1.5">
                        MiFantasy
                        <span class="text-[8px] uppercase font-bold tracking-widest text-lime-400 bg-lime-400/10 px-1 py-0 rounded border border-lime-400/20">PRO</span>
                    </span>
                    <span class="text-[10px] text-zinc-400 -mt-1 tracking-wider uppercase font-semibold hidden sm:block">Liga & Fantasy SaaS</span>
                </div>
            </a>

            <!-- Central Nav Links with hover pill indicator -->
            <nav class="hidden md:flex items-center gap-1 text-sm font-medium text-zinc-400 bg-zinc-900/60 border border-zinc-800/80 px-4 py-1.5 rounded-full shadow-inner">
                <a href="#experiencia" class="px-3.5 py-1.5 rounded-full transition-all duration-200 hover:text-white hover:bg-zinc-800/80">Experiencia</a>
                <a href="#features" class="px-3.5 py-1.5 rounded-full transition-all duration-200 hover:text-white hover:bg-zinc-800/80">Características</a>
                <a href="#modalidades" class="px-3.5 py-1.5 rounded-full transition-all duration-200 hover:text-white hover:bg-zinc-800/80">Modalidades</a>
                <a href="#pricing" class="px-3.5 py-1.5 rounded-full transition-all duration-200 hover:text-white hover:bg-zinc-800/80">Planes</a>
                <a href="#faq" class="px-3.5 py-1.5 rounded-full transition-all duration-200 hover:text-white hover:bg-zinc-800/80">FAQ</a>
            </nav>

            <!-- Auth action CTA -->
            <div class="flex items-center gap-1 sm:gap-3">
                @auth
                    <a href="{{ route('home') }}" class="group relative inline-flex items-center justify-center px-2 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-semibold text-zinc-100 bg-zinc-900 border border-zinc-800 rounded-xl transition-all duration-300 hover:border-lime-400/50 hover:bg-zinc-800/80 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-lime-400 mr-1.5 sm:mr-2 animate-pulse"></span>
                        Mi Panel
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm font-medium text-zinc-300 transition-colors duration-200 hover:text-white px-1.5 sm:px-3 py-1.5">
                        Acceder
                    </a>
                    <a href="{{ route('register') }}" class="relative inline-flex items-center justify-center whitespace-nowrap px-2 py-1.5 text-xs sm:px-4 sm:text-sm font-bold text-zinc-950 bg-lime-400 rounded-xl transition-all duration-300 ease-out hover:bg-lime-300 hover:scale-[1.02] shadow-[0_0_20px_rgba(163,230,53,0.35)] active:scale-[0.98]">
                        Crear Liga
                        <svg class="ml-1 w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform duration-200 group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 z-10">

        <!-- ================= HERO SECTION ================= -->
        <section class="relative pt-16 pb-20 md:pt-24 md:pb-32 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">

                <!-- Hero Header Copy -->
                <div class="max-w-4xl mx-auto text-center flex flex-col items-center">

                    <!-- Live SaaS Pill Badge -->
                    <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-zinc-900/90 border border-zinc-800/90 text-xs font-semibold text-zinc-300 mb-8 shadow-sm backdrop-blur-md">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-lime-400"></span>
                        </span>
                        <span class="text-zinc-200">Temporada 2026/27</span>
                        <span class="text-zinc-600">|</span>
                        <span class="text-lime-400 flex items-center gap-1 font-mono">
                            Multi-Tenant Isolado & F11 · F7 · Sala
                        </span>
                    </div>

                    <!-- Main Catchphrase Headline -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-black tracking-tight text-white leading-[1.06] mb-8">
                        La plataforma definitiva para <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-400 via-emerald-300 to-teal-200">
                            dominar tu liga de fútbol
                        </span>
                    </h1>

                    <!-- Clear Subtitle -->
                    <p class="max-w-2xl text-lg sm:text-xl text-zinc-400 font-normal leading-relaxed mb-10">
                        Olvida las hojas de cálculo y grupos de WhatsApp caóticos. Genera alineaciones interactivas, puntuaciones automatizadas por jornada y gestiona cobros con Bizum en un solo lugar.
                    </p>

                    <!-- CTA Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto mb-16">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold text-zinc-950 bg-lime-400 hover:bg-lime-300 rounded-2xl transition-all duration-300 shadow-[0_0_30px_rgba(163,230,53,0.4)] hover:shadow-[0_0_45px_rgba(163,230,53,0.6)] hover:-translate-y-0.5 active:translate-y-0">
                            <span>Lanza tu liga gratis</span>
                            <svg class="ml-2 w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                        <a href="#experiencia" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-zinc-300 hover:text-white bg-zinc-900/90 border border-zinc-800 hover:border-zinc-700 rounded-2xl transition-all duration-300 hover:-translate-y-0.5 backdrop-blur-md">
                            <svg class="w-5 h-5 mr-2 text-lime-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="10 8 16 12 10 16 10 8"></polygon>
                            </svg>
                            Ver demo en vivo
                        </a>
                    </div>
                </div>

                <!-- ================= INTERACTIVE PITCH HERO COMPONENT ================= -->
                <div id="experiencia" class="relative max-w-5xl mx-auto rounded-3xl p-1 bg-gradient-to-b from-zinc-700/40 via-zinc-800/20 to-zinc-900/60 shadow-2xl">
                    <div class="relative rounded-[22px] bg-zinc-950 border border-zinc-800/80 overflow-hidden">

                        <!-- Top Chrome Bar -->
                        <div class="px-6 py-4 border-b border-zinc-800/80 bg-zinc-900/80 flex items-center justify-between text-xs text-zinc-400">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                                <span class="ml-2 font-mono text-zinc-400 hidden sm:inline">mifantasy.app/liga/copa-veteranos/jornada-18</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-lime-400/10 text-lime-400 border border-lime-400/20 font-semibold text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse"></span>
                                    En Vivo · Min 84'
                                </span>
                                <span class="text-zinc-500 font-mono hidden md:inline">Táctica: 4-3-3 Ofensiva</span>
                            </div>
                        </div>

                        <!-- Pitch Showcase Area -->
                        <div class="relative p-6 sm:p-10 soccer-pitch min-h-[520px] flex flex-col justify-between items-center overflow-hidden">

                            <!-- Tactical Soccer Pitch Markings (SVG Vector Precision) -->
                            <div class="absolute inset-4 rounded-2xl border-2 pitch-line pointer-events-none">
                                <!-- Half-way Line -->
                                <div class="absolute top-1/2 left-0 right-0 h-0.5 pitch-line border-t-2"></div>
                                <!-- Center Circle -->
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 rounded-full border-2 pitch-line"></div>
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-lime-400/40"></div>
                                <!-- Penalty Areas -->
                                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-24 border-b-2 border-l-2 border-r-2 pitch-line"></div>
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-64 h-24 border-t-2 border-l-2 border-r-2 pitch-line"></div>
                            </div>

                            <!-- Floating Live Event Cards (Left) -->
                            <div class="absolute top-8 left-6 hidden lg:flex flex-col gap-3 z-20">
                                <div class="glass-panel-glow rounded-xl p-3.5 max-w-[210px] text-xs">
                                    <div class="flex items-center justify-between text-zinc-400 mb-1">
                                        <span class="font-semibold text-lime-400 flex items-center gap-1">
                                            ⚽ ¡GOL FANTASY!
                                        </span>
                                        <span class="font-mono text-[10px]">Min 78'</span>
                                    </div>
                                    <p class="font-bold text-white text-sm">Marcos Asensio</p>
                                    <p class="text-[11px] text-zinc-400">+8.5 pts asignados en vivo</p>
                                </div>

                                <div class="glass-panel rounded-xl p-3 max-w-[210px] text-xs">
                                    <div class="text-[10px] uppercase font-mono text-zinc-400">Puntos Totales Jornada</div>
                                    <div class="text-xl font-extrabold text-lime-400 mt-0.5">87.5 <span class="text-xs text-zinc-400 font-normal">pts (#1 Liga)</span></div>
                                </div>
                            </div>

                            <!-- Floating Live Event Cards (Right) -->
                            <div class="absolute top-8 right-6 hidden lg:flex flex-col gap-3 z-20 text-right">
                                <div class="glass-panel rounded-xl p-3.5 max-w-[220px] text-xs text-left">
                                    <div class="flex items-center justify-between text-zinc-400 mb-1">
                                        <span class="text-[10px] font-mono text-zinc-500">MERCADO ACTIVO</span>
                                        <span class="text-lime-400 font-semibold text-[10px]">Cláusula Pagada</span>
                                    </div>
                                    <p class="font-semibold text-white">Traspaso Directo</p>
                                    <p class="text-[11px] text-zinc-400">Bizum conciliado 14,000,000€</p>
                                </div>
                            </div>

                            <!-- Dynamic 4-3-3 Lineup Representation on Pitch -->
                            <div class="w-full max-w-2xl z-10 flex flex-col justify-between h-[420px] my-auto">

                                <!-- Attackers Line (DEL) -->
                                <div class="flex justify-around items-center">
                                    <!-- Player 1 -->
                                    <div class="group relative flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="relative w-12 h-12 rounded-full bg-zinc-900 border-2 border-lime-400/80 flex items-center justify-center shadow-lg group-hover:shadow-[0_0_20px_rgba(163,230,53,0.6)]">
                                            <span class="font-black text-xs text-white">7</span>
                                            <span class="absolute -top-1 -right-1 bg-lime-400 text-zinc-950 font-extrabold text-[9px] w-4 h-4 rounded-full flex items-center justify-center">C</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Vinícius (16p)</span>
                                    </div>

                                    <!-- Player 2 -->
                                    <div class="group relative flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-12 h-12 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">9</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Lewandowski (11p)</span>
                                    </div>

                                    <!-- Player 3 -->
                                    <div class="group relative flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-12 h-12 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">11</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Nico Williams (9p)</span>
                                    </div>
                                </div>

                                <!-- Midfielders Line (MED) -->
                                <div class="flex justify-around items-center px-6">
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">8</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Pedri (10p)</span>
                                    </div>
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">14</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Valverde (8p)</span>
                                    </div>
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">22</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Isco (12p)</span>
                                    </div>
                                </div>

                                <!-- Defenders Line (DEF) -->
                                <div class="flex justify-between items-center px-4">
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">2</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Carvajal (7p)</span>
                                    </div>
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">4</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Araújo (9p)</span>
                                    </div>
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">5</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Vivian (8p)</span>
                                    </div>
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-11 h-11 rounded-full bg-zinc-900 border-2 border-zinc-400/80 flex items-center justify-center shadow-lg group-hover:border-lime-400">
                                            <span class="font-black text-xs text-white">3</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Gayà (6p)</span>
                                    </div>
                                </div>

                                <!-- Goalkeeper (POR) -->
                                <div class="flex justify-center items-center">
                                    <div class="group flex flex-col items-center w-20 cursor-pointer transition-transform duration-200 hover:scale-110">
                                        <div class="w-12 h-12 rounded-full bg-amber-500/90 border-2 border-white flex items-center justify-center shadow-lg">
                                            <span class="font-black text-xs text-zinc-950">1</span>
                                        </div>
                                        <span class="mt-1 w-full max-w-[80px] text-[9px] font-bold text-white bg-zinc-900/90 px-1 py-0.5 rounded border border-zinc-800 truncate text-center block">Courtois (+8p)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Tactician Control Pill -->
                            <div class="w-full flex items-center justify-between pt-6 border-t border-zinc-800/60 z-10 text-xs">
                                <div class="flex items-center gap-2 text-zinc-300 font-mono">
                                    <span class="w-2 h-2 rounded-full bg-lime-400"></span>
                                    Alineación validada sin sanciones
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-zinc-400 hidden sm:inline">Presupuesto restante:</span>
                                    <span class="font-bold text-lime-400 bg-lime-400/10 px-2.5 py-1 rounded-lg border border-lime-400/20 font-mono">
                                        12.4M €
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Proof Metrics Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-16 max-w-5xl mx-auto border-t border-zinc-900 mt-16 text-center">
                    <div class="p-4 rounded-2xl bg-zinc-900/30 border border-zinc-800/40">
                        <div class="text-3xl sm:text-4xl font-black text-white tracking-tight">100%</div>
                        <div class="text-xs uppercase font-semibold tracking-wider text-zinc-500 mt-1">Multi-Tenant Aislado</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-zinc-900/30 border border-zinc-800/40">
                        <div class="text-3xl sm:text-4xl font-black text-lime-400 tracking-tight">&lt; 0.4s</div>
                        <div class="text-xs uppercase font-semibold tracking-wider text-zinc-500 mt-1">Cálculo de Jornada</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-zinc-900/30 border border-zinc-800/40">
                        <div class="text-3xl sm:text-4xl font-black text-white tracking-tight">F11 · F7 · Sala</div>
                        <div class="text-xs uppercase font-semibold tracking-wider text-zinc-500 mt-1">Formatos Oficiales</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-zinc-900/30 border border-zinc-800/40">
                        <div class="text-3xl sm:text-4xl font-black text-white tracking-tight">Bizum + Stripe</div>
                        <div class="text-xs uppercase font-semibold tracking-wider text-zinc-500 mt-1">Cobro y Conciliación</div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ================= FEATURES BENTO GRID SECTION ================= -->
        <section id="features" class="py-24 md:py-32 border-t border-zinc-900 relative">
            <div class="max-w-7xl mx-auto px-6">

                <div class="max-w-3xl mb-16">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-lime-400/10 text-lime-400 text-xs font-mono font-semibold mb-4 border border-lime-400/20">
                        ARQUITECTURA DE COMPETICIÓN
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-6">
                        Todo lo que tu torneo necesita, ejecutado con precisión quirúrgica.
                    </h2>
                    <p class="text-base sm:text-lg text-zinc-400">
                        Diseñado desde el principio para erradicar las hojas de Excel y proporcionar una experiencia fluida a organizadores, árbitros y jugadores.
                    </p>
                </div>

                <!-- Asymmetric Bento Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Bento 1: Large Span (Interactive Formation Builder) -->
                    <div class="md:col-span-2 glass-panel rounded-3xl p-8 sm:p-10 flex flex-col justify-between transition-all duration-300 hover:border-lime-400/40 group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-lime-400/10 border border-lime-400/20 flex items-center justify-center text-lime-400 mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                    <line x1="9" y1="21" x2="9" y2="9"></line>
                                </svg>
                            </div>
                            <span class="text-xs font-mono uppercase tracking-wider text-lime-400 font-semibold">Táctica & Alineaciones</span>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1 mb-3">
                                Motor de Alineación Dinámica
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm sm:text-base max-w-xl mb-8">
                                Confecciona tu once titular con restricciones de presupuesto, posiciones naturales y capitanes con puntuación doble. Validación en tiempo real para impedir alineaciones indebidas.
                            </p>
                        </div>

                        <!-- Mini Interactive Tactical Badge Preview -->
                        <div class="rounded-2xl bg-zinc-950/80 border border-zinc-800/80 p-5 flex flex-wrap items-center justify-between gap-4 font-mono text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-lime-400"></div>
                                <span class="text-zinc-300">Esquemas nativos: 4-3-3 · 4-4-2 · 3-5-2 · 3-2-1 · 2-2-1</span>
                            </div>
                            <div class="text-lime-400 font-bold bg-lime-400/10 px-3 py-1 rounded-lg border border-lime-400/20">
                                Restricción 100M€ / Cero Errores
                            </div>
                        </div>
                    </div>

                    <!-- Bento 2: Single Span (Instant Payments with Bizum) -->
                    <div class="glass-panel rounded-3xl p-8 sm:p-10 flex flex-col justify-between transition-all duration-300 hover:border-lime-400/40 group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-lime-400 mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-mono uppercase tracking-wider text-zinc-500 font-semibold">Finanzas & Cobro</span>
                            <h3 class="text-2xl font-extrabold text-white mt-1 mb-3">
                                Cobros con Bizum y Tarjeta
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm">
                                Integra cuotas de inscripción por equipo o suscripciones recurrentes con conciliación automática mediante Stripe y Bizum.
                            </p>
                        </div>

                        <div class="pt-6 border-t border-zinc-800/80 flex items-center justify-between text-xs">
                            <span class="text-zinc-400 font-mono">Cumplimiento PSD2</span>
                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 font-semibold border border-emerald-500/20">
                                0% Cuadre manual
                            </span>
                        </div>
                    </div>

                    <!-- Bento 3: Single Span (Multi-Tenant Organization) -->
                    <div class="glass-panel rounded-3xl p-8 sm:p-10 flex flex-col justify-between transition-all duration-300 hover:border-lime-400/40 group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-lime-400 mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-mono uppercase tracking-wider text-zinc-500 font-semibold">Multi-Tenant RBAC</span>
                            <h3 class="text-2xl font-extrabold text-white mt-1 mb-3">
                                Espacio 100% Exclusivo
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm">
                                Cada liga cuenta con base de datos lógica y estadísticas completamente aisladas. Permisos granulares para árbitros, delegados y directores de torneo.
                            </p>
                        </div>

                        <div class="pt-6 border-t border-zinc-800/80">
                            <span class="font-mono text-xs text-zinc-500 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-lime-400"></span>
                                Aislamiento Estricto por Tenant
                            </span>
                        </div>
                    </div>

                    <!-- Bento 4: Large Span (Live Ref Match Reports & Scores) -->
                    <div class="md:col-span-2 glass-panel rounded-3xl p-8 sm:p-10 flex flex-col justify-between transition-all duration-300 hover:border-lime-400/40 group">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-lime-400/10 border border-lime-400/20 flex items-center justify-center text-lime-400 mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                            </div>
                            <span class="text-xs font-mono uppercase tracking-wider text-lime-400 font-semibold">Algoritmo en Tiempo Real</span>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-white mt-1 mb-3">
                                Actas Arbitrales y Puntuación en Vivo
                            </h3>
                            <p class="text-zinc-400 leading-relaxed text-sm sm:text-base max-w-xl mb-8">
                                Al cargar el acta arbitral con goles, asistencias y tarjetas, el sistema recalcula inmediatamente las tablas de clasificación y los puntos fantasy de todos los participantes.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-zinc-950/80 border border-zinc-800/80 p-5 flex items-center justify-between text-xs font-mono">
                            <div class="flex items-center gap-3">
                                <span class="text-lime-400 font-bold">Transacciones Atómicas:</span>
                                <span class="text-zinc-400">Cálculo de jornada en menos de 400 milisegundos.</span>
                            </div>
                            <span class="text-zinc-500 hidden sm:inline">Cero desfases</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= MODALIDADES DEPORTIVAS ================= -->
        <section id="modalidades" class="py-24 md:py-32 border-t border-zinc-900 bg-zinc-950/50">
            <div class="max-w-7xl mx-auto px-6">

                <div class="max-w-3xl mb-16">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-zinc-900 text-zinc-300 text-xs font-mono font-semibold mb-4 border border-zinc-800">
                        VERSATILIDAD DEPORTIVA
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white mb-6">
                        Adaptado a cualquier formato y reglamento.
                    </h2>
                    <p class="text-base sm:text-lg text-zinc-400">
                        Cada modalidad cuenta con su propio motor de puntuaciones, reglas de cambios y plantillas configurables.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Fútbol 11 -->
                    <div class="glass-panel rounded-3xl p-8 transition-all duration-300 hover:-translate-y-1 hover:border-lime-400/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="text-xs font-mono uppercase tracking-wider text-lime-400 bg-lime-400/10 px-2.5 py-1 rounded border border-lime-400/20 font-bold">
                                    01 · Tradicional
                                </span>
                                <span class="text-zinc-500 font-mono text-xs">11 vs 11</span>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white mb-3">Fútbol 11</h3>
                            <p class="text-sm text-zinc-400 leading-relaxed mb-6">
                                Torneos estándar federados, ligas de fin de semana y copas de eliminación. Plantillas completas de 18 a 25 futbolistas y esquemas tácticos clásicos.
                            </p>
                        </div>
                        <ul class="space-y-2.5 text-xs text-zinc-300 border-t border-zinc-800/80 pt-6">
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Alineación clásica con 11 titulares y banquillo
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Estadísticas avanzadas de portería a cero
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Soporte para árbitro principal y linieres
                            </li>
                        </ul>
                    </div>

                    <!-- Fútbol 7 -->
                    <div class="glass-panel rounded-3xl p-8 transition-all duration-300 hover:-translate-y-1 hover:border-lime-400/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="text-xs font-mono uppercase tracking-wider text-lime-400 bg-lime-400/10 px-2.5 py-1 rounded border border-lime-400/20 font-bold">
                                    02 · Dinámico
                                </span>
                                <span class="text-zinc-500 font-mono text-xs">7 vs 7</span>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white mb-3">Fútbol 7</h3>
                            <p class="text-sm text-zinc-400 leading-relaxed mb-6">
                                El formato más popular en ligas de empresa, veteranos y torneos nocturnos. Rotaciones ágiles, altas puntuaciones y actas simplificadas.
                            </p>
                        </div>
                        <ul class="space-y-2.5 text-xs text-zinc-300 border-t border-zinc-800/80 pt-6">
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Esquemas optimizados (3-2-1, 2-3-1, 3-1-2)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Cambios volantes ilimitados en acta
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Algoritmo de goles por minuto
                            </li>
                        </ul>
                    </div>

                    <!-- Fútbol Sala -->
                    <div class="glass-panel rounded-3xl p-8 transition-all duration-300 hover:-translate-y-1 hover:border-lime-400/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <span class="text-xs font-mono uppercase tracking-wider text-lime-400 bg-lime-400/10 px-2.5 py-1 rounded border border-lime-400/20 font-bold">
                                    03 · Alta Intensidad
                                </span>
                                <span class="text-zinc-500 font-mono text-xs">5 vs 5</span>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white mb-3">Fútbol Sala</h3>
                            <p class="text-sm text-zinc-400 leading-relaxed mb-6">
                                Ritmo vertiginoso en pista cubierta. Control milimétrico de faltas acumuladas, portero-jugador y máxima frecuencia goleadora.
                            </p>
                        </div>
                        <ul class="space-y-2.5 text-xs text-zinc-300 border-t border-zinc-800/80 pt-6">
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Esquemas de pista (1-2-1 rombo, 2-2 cuadrado)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Registro de doble penaltis y paradas clave
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-lime-400">✓</span> Valoración MVP por rendimiento efectivo
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= CÓMO FUNCIONA / WORKFLOW ================= -->
        <section class="py-24 md:py-32 border-t border-zinc-900">
            <div class="max-w-7xl mx-auto px-6">

                <div class="text-center max-w-2xl mx-auto mb-20">
                    <span class="text-xs font-mono uppercase tracking-widest text-lime-400 font-bold">PASO A PASO</span>
                    <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white mt-3 mb-4">
                        Del registro al saque inicial en 3 minutos.
                    </h2>
                    <p class="text-zinc-400 text-sm sm:text-base">
                        Automatización completa para que te enfoques únicamente en la emoción del juego.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">

                    <!-- Step 1 -->
                    <div class="relative glass-panel rounded-3xl p-8 flex flex-col items-start">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center font-black text-xl text-lime-400 mb-6 font-mono">
                            01
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Crea tu Organización</h3>
                        <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                            Regístrate y aprovisiona el espacio de tu torneo. Define el nombre, logo, modalidades deseadas y activa cobros automáticos si lo requieres.
                        </p>
                        <span class="mt-auto text-xs font-mono text-zinc-500">⏱ Tiempo: ~60 segundos</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative glass-panel rounded-3xl p-8 flex flex-col items-start">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center font-black text-xl text-lime-400 mb-6 font-mono">
                            02
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Invita Equipos & Calendario</h3>
                        <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                            Genera el rol de partidos de forma automática con ida y vuelta o eliminación directa. Comparte el enlace de invitación para que los capitanes inscriban sus plantillas.
                        </p>
                        <span class="mt-auto text-xs font-mono text-zinc-500">⏱ Generador de 1 clic</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative glass-panel-glow rounded-3xl p-8 flex flex-col items-start">
                        <div class="w-12 h-12 rounded-2xl bg-lime-400 text-zinc-950 flex items-center justify-center font-black text-xl mb-6 font-mono">
                            03
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Puntuaciones & Fantasy</h3>
                        <p class="text-zinc-300 text-sm leading-relaxed mb-6">
                            Los árbitros suben el resultado y el motor asigna los puntos fantasy a cada entrenador según los goles, paradas y asistencias de sus futbolistas alineados.
                        </p>
                        <span class="mt-auto text-xs font-mono text-lime-400 font-semibold">⚡ En tiempo real</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= PRICING SECTION ================= -->
        <section id="pricing" class="py-24 md:py-32 border-t border-zinc-900 relative">
            <div class="max-w-7xl mx-auto px-6">

                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-mono uppercase tracking-widest text-lime-400 font-bold">PRECIO CLARO Y DIRECTO</span>
                    <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white mt-3 mb-4">
                        Una tarifa plana. Sin sorpresas.
                    </h2>
                    <p class="text-zinc-400 text-sm sm:text-base">
                        Todo lo que necesitas para tu temporada completa sin comisiones ocultas ni cobros abusivos por jugador.
                    </p>
                </div>

                <!-- Single High-Converting Pricing Card -->
                <div class="max-w-xl mx-auto glass-panel-glow rounded-[32px] p-8 sm:p-12 relative overflow-hidden">

                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <span class="text-xs font-mono uppercase tracking-wider text-lime-400 font-bold">PLAN PRO SAAS</span>
                            <h3 class="text-2xl font-extrabold text-white mt-0.5">Organizador de Liga</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-lime-400/20 text-lime-400 border border-lime-400/30">
                            Bizum + Tarjeta
                        </span>
                    </div>

                    <div class="flex items-baseline gap-2 mb-6">
                        <span class="text-6xl sm:text-7xl font-black tracking-tight text-white">9€</span>
                        <span class="text-zinc-400 text-base font-semibold">/ mes</span>
                    </div>

                    <p class="text-sm text-zinc-300 leading-relaxed mb-8">
                        Diseñado para ligas amateur, asociaciones deportivas, colegios, ayuntamientos y torneos corporativos.
                    </p>

                    <div class="space-y-4 mb-10 text-sm text-zinc-200">
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                            <span>Hasta 16 equipos por competición</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                            <span>Motor Fantasy dinámico con puntos en vivo</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                            <span>Alineaciones interactivas F11, F7 y Sala</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                            <span>Gestión de actas, goleadores, amarillas y rojas</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                            <span>Portal de facturación y recibos descargables</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-lime-400/20 text-lime-400 flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                            <span>Cancelación en 1 clic sin permanencia</span>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-8 py-4.5 text-base font-bold text-zinc-950 bg-lime-400 hover:bg-lime-300 rounded-2xl transition-all duration-300 shadow-[0_0_25px_rgba(163,230,53,0.4)] hover:shadow-[0_0_40px_rgba(163,230,53,0.6)] hover:-translate-y-0.5">
                        Empezar Ahora por 9€/mes
                        <svg class="ml-2 w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>

                    <div class="mt-6 flex items-center justify-center gap-2 text-xs text-zinc-400">
                        <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Garantía de reembolso de 14 días. Sin preguntas.</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= FAQ ACCORDION SECTION ================= -->
        <section id="faq" class="py-24 md:py-32 border-t border-zinc-900 bg-zinc-950/40">
            <div class="max-w-4xl mx-auto px-6">

                <div class="max-w-2xl mb-16">
                    <span class="text-xs font-mono uppercase tracking-widest text-lime-400 font-bold">PREGUNTAS FRECUENTES</span>
                    <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white mt-3 mb-4">
                        Respuestas claras a tus dudas.
                    </h2>
                    <p class="text-zinc-400 text-sm sm:text-base">
                        Todo lo que necesitas conocer para empezar con tranquilidad.
                    </p>
                </div>

                <div class="space-y-4">

                    <!-- Question 1 -->
                    <div class="glass-panel rounded-2xl p-6 transition-all duration-300">
                        <button type="button" class="faq-toggle w-full flex items-center justify-between text-left focus:outline-none" onclick="toggleFaq(1)">
                            <span class="text-base sm:text-lg font-bold text-white">¿Cómo funciona la integración de pago con Bizum y tarjeta?</span>
                            <div class="faq-icon w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 ml-4 shrink-0" id="faq-icon-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </button>
                        <div class="faq-answer open mt-4" id="faq-answer-1">
                            <p class="text-sm text-zinc-400 leading-relaxed border-t border-zinc-800/80 pt-4">
                                Procesamos los cobros de forma completamente segura mediante Stripe Checkout en cumplimiento con la normativa PSD2. Puedes suscribirte mediante cualquier tarjeta de débito/crédito o seleccionando Bizum en la pasarela. El aprovisionamiento de tu liga es instantáneo y seguro.
                            </p>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="glass-panel rounded-2xl p-6 transition-all duration-300">
                        <button type="button" class="faq-toggle w-full flex items-center justify-between text-left focus:outline-none" onclick="toggleFaq(2)">
                            <span class="text-base sm:text-lg font-bold text-white">¿Puedo gestionar varias competiciones con distintas modalidades a la vez?</span>
                            <div class="faq-icon w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 ml-4 shrink-0" id="faq-icon-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </button>
                        <div class="faq-answer mt-4" id="faq-answer-2">
                            <p class="text-sm text-zinc-400 leading-relaxed border-t border-zinc-800/80 pt-4">
                                Absolutamente. Desde tu panel de control como organizador puedes dar de alta competiciones simultáneas de Fútbol 11, Fútbol 7 y Fútbol Sala, cada una con sus propias reglas y plantillas.
                            </p>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="glass-panel rounded-2xl p-6 transition-all duration-300">
                        <button type="button" class="faq-toggle w-full flex items-center justify-between text-left focus:outline-none" onclick="toggleFaq(3)">
                            <span class="text-base sm:text-lg font-bold text-white">¿Tienen permanencia las suscripciones?</span>
                            <div class="faq-icon w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 ml-4 shrink-0" id="faq-icon-3">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </button>
                        <div class="faq-answer mt-4" id="faq-answer-3">
                            <p class="text-sm text-zinc-400 leading-relaxed border-t border-zinc-800/80 pt-4">
                                No. Puedes cancelar tu suscripción en cualquier momento con un solo clic desde el Customer Portal de Stripe integrado en tu perfil.
                            </p>
                        </div>
                    </div>

                    <!-- Question 4 -->
                    <div class="glass-panel rounded-2xl p-6 transition-all duration-300">
                        <button type="button" class="faq-toggle w-full flex items-center justify-between text-left focus:outline-none" onclick="toggleFaq(4)">
                            <span class="text-base sm:text-lg font-bold text-white">¿Cómo acceden los árbitros para subir las actas?</span>
                            <div class="faq-icon w-8 h-8 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 ml-4 shrink-0" id="faq-icon-4">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </button>
                        <div class="faq-answer mt-4" id="faq-answer-4">
                            <p class="text-sm text-zinc-400 leading-relaxed border-t border-zinc-800/80 pt-4">
                                El sistema te permite generar roles de Árbitro con acceso restringido exclusivamente a los partidos que les han sido asignados. Desde su móvil pueden firmar el acta al finalizar el encuentro, registrar goleadores, tarjetas y MVP sin tener acceso a la administración ni a la facturación de la liga.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= FINAL HIGH-IMPACT CTA BANNER ================= -->
        <section class="py-20 md:py-28 relative">
            <div class="max-w-6xl mx-auto px-6">
                <div class="relative rounded-[36px] bg-gradient-to-b from-zinc-900 via-zinc-900/90 to-zinc-950 border border-zinc-800 p-10 sm:p-16 text-center overflow-hidden shadow-2xl">

                    <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 hero-glow-sphere pointer-events-none"></div>

                    <div class="relative z-10 max-w-2xl mx-auto">
                        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-lime-400/10 text-lime-400 text-xs font-mono font-bold mb-6 border border-lime-400/20">
                            CREA TU TORNEO EN MINUTOS
                        </span>

                        <h2 class="text-3xl sm:text-5xl md:text-6xl font-black text-white tracking-tight leading-tight mb-6">
                            Lleva tu competición al nivel que siempre mereció.
                        </h2>

                        <p class="text-base sm:text-lg text-zinc-400 mb-10 leading-relaxed">
                            Únete a cientos de organizadores y miles de jugadores que ya compiten con tecnología profesional.
                        </p>

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-9 py-4.5 text-base font-bold text-zinc-950 bg-lime-400 hover:bg-lime-300 rounded-2xl transition-all duration-300 shadow-[0_0_35px_rgba(163,230,53,0.4)] hover:shadow-[0_0_50px_rgba(163,230,53,0.6)] hover:-translate-y-0.5">
                                Empezar ahora gratis
                                <svg class="ml-2 w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4.5 text-base font-semibold text-zinc-300 hover:text-white bg-zinc-900 border border-zinc-800 rounded-2xl transition-all duration-300 hover:border-zinc-700">
                                Iniciar sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="border-t border-zinc-900/80 bg-zinc-950 py-12 z-10">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-8">

            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-lime-400">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold text-white">MiFantasy SaaS</span>
                    <span class="text-zinc-600 mx-2">·</span>
                    <span class="text-xs text-zinc-500">© {{ date('Y') }} Todos los derechos reservados.</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-6 text-xs text-zinc-400 font-medium">
                <a href="#experiencia" class="hover:text-white transition-colors">Experiencia</a>
                <a href="#features" class="hover:text-white transition-colors">Características</a>
                <a href="#modalidades" class="hover:text-white transition-colors">Modalidades</a>
                <a href="#pricing" class="hover:text-white transition-colors">Precios</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Acceso Clientes</a>
            </div>

            <div class="flex items-center gap-2 text-xs text-zinc-500 font-mono">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Todos los sistemas operativos</span>
            </div>

        </div>
    </footer>

</div>

<!-- Interactive Client-side Script: Accordion & GSAP Scroll Reveals -->
<script>
    // FAQ Accordion Handler
    function toggleFaq(index) {
        const answer = document.getElementById(`faq-answer-${index}`);
        const icon = document.getElementById(`faq-icon-${index}`);

        if (!answer || !icon) return;

        const isOpen = answer.classList.contains('open');

        // Close all
        document.querySelectorAll('.faq-answer').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.faq-icon').forEach(el => el.classList.remove('rotated'));

        // Toggle current if it was closed
        if (!isOpen) {
            answer.classList.add('open');
            icon.classList.add('rotated');
        }
    }

    // GSAP Scroll Reveals (Smooth physical reveal effects)
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
            gsap.registerPlugin(ScrollTrigger);

            // Respect prefers-reduced-motion
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            // Reveal cards and sections smoothly
            const revealElements = document.querySelectorAll('.glass-panel, .glass-panel-glow');
            revealElements.forEach((el) => {
                gsap.from(el, {
                    opacity: 0,
                    y: 18,
                    duration: 0.6,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 88%',
                        toggleActions: 'play none none reverse'
                    }
                });
            });
        }
    });
</script>
@endsection
