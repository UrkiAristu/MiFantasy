@extends('layouts.base')

@section('head')
    <title>MiFantasy - La Plataforma SaaS B2B para Ligas y Torneos Amateur</title>
    <meta name="description" content="Gestiona y profesionaliza tus torneos de Fútbol 11, Fútbol 7 y Fútbol Sala con MiFantasy. Software SaaS multi-tenant con motor de alineaciones y puntuaciones automáticas.">

    <!-- Google Fonts: Outfit & Plus Jakarta Sans (Sport-Tech Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos base de la aplicación -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/fantasy.css') }}" rel="stylesheet" />

    <style>
        :root {
            /* Paleta Sport-Tech: Fondos Navy/Slate Ultra Oscuros */
            --mf-bg-dark: #070b14;
            --mf-bg-surface: #0e1526;
            --mf-card-bg: #111a2e;
            --mf-card-bg-elevated: #18233c;
            --mf-card-border: rgba(255, 255, 255, 0.08);
            --mf-card-border-hover: rgba(0, 255, 135, 0.4);

            /* Acentos Neón / Verde Lima Vibrante */
            --mf-lime: #00ff87;
            --mf-lime-hover: #00e077;
            --mf-lime-glow: rgba(0, 255, 135, 0.35);
            --mf-lime-dark: #062b1d;
            --mf-emerald: #10b981;
            --mf-cyan: #38bdf8;
            --mf-blue: #3b82f6;

            /* Tipografía y Textos */
            --mf-text-main: #f8fafc;
            --mf-text-muted: #94a3b8;
            --mf-text-subtle: #64748b;

            /* Bordes Redondeados Estilo App Nativa */
            --mf-radius-sm: 0.625rem;
            --mf-radius-md: 1rem;
            --mf-radius-lg: 1.5rem;
            --mf-radius-xl: 2rem;

            /* Sombras Suaves y Resplandor Sport */
            --mf-shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.25);
            --mf-shadow-md: 0 12px 30px -6px rgba(0, 0, 0, 0.45);
            --mf-shadow-lg: 0 20px 45px -10px rgba(0, 0, 0, 0.6);
            --mf-shadow-neon: 0 0 35px rgba(0, 255, 135, 0.25), 0 15px 30px -5px rgba(0, 255, 135, 0.2);
        }

        body {
            background-color: var(--mf-bg-dark);
            color: var(--mf-text-muted);
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--mf-text-main);
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.025em;
        }

        /* Navbar Sport-Tech */
        .landing-navbar {
            background: rgba(7, 11, 20, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--mf-card-border);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .nav-link {
            color: var(--mf-text-muted) !important;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s ease;
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--mf-lime) !important;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            padding-top: 5.5rem;
            padding-bottom: 5.5rem;
            background:
                radial-gradient(circle at 50% 10%, rgba(0, 255, 135, 0.1) 0%, rgba(7, 11, 20, 0) 65%),
                radial-gradient(circle at 85% 30%, rgba(56, 189, 248, 0.08) 0%, rgba(7, 11, 20, 0) 50%);
        }

        .hero-badge {
            background: rgba(0, 255, 135, 0.1);
            color: var(--mf-lime);
            border: 1px solid rgba(0, 255, 135, 0.28);
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 0 20px rgba(0, 255, 135, 0.15);
        }

        .hero-title {
            font-size: clamp(2.4rem, 5.5vw, 4.2rem);
            font-weight: 800;
            line-height: 1.12;
            color: #ffffff;
        }

        .text-gradient {
            background: linear-gradient(135deg, #00ff87 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-gradient-alt {
            background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Botones Sport-Tech */
        .btn-mf-primary {
            background: linear-gradient(135deg, #00ff87 0%, #10b981 100%);
            border: none;
            color: #04140d !important;
            font-weight: 700;
            padding: 0.85rem 1.85rem;
            border-radius: var(--mf-radius-md);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -4px rgba(0, 255, 135, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-mf-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px -4px rgba(0, 255, 135, 0.55);
            background: linear-gradient(135deg, #1aff94 0%, #059669 100%);
            color: #04140d !important;
        }

        .btn-mf-outline {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #ffffff !important;
            font-weight: 600;
            padding: 0.85rem 1.85rem;
            border-radius: var(--mf-radius-md);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-mf-outline:hover {
            background: rgba(255, 255, 255, 0.09);
            border-color: rgba(255, 255, 255, 0.35);
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        /* Mockup Container */
        .mockup-container {
            background: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: var(--mf-radius-lg);
            box-shadow: var(--mf-shadow-lg);
            overflow: hidden;
            position: relative;
        }

        .mockup-header {
            background: rgba(7, 11, 20, 0.7);
            padding: 0.9rem 1.35rem;
            border-bottom: 1px solid var(--mf-card-border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mockup-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Feature Cards */
        .feature-card {
            background: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: var(--mf-radius-lg);
            padding: 2.5rem 2rem;
            height: 100%;
            box-shadow: var(--mf-shadow-sm);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: var(--mf-card-border-hover);
            box-shadow: var(--mf-shadow-md), 0 0 25px rgba(0, 255, 135, 0.1);
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: var(--mf-radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            margin-bottom: 1.5rem;
        }

        /* Pricing Cards */
        .pricing-card {
            background: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: var(--mf-radius-lg);
            padding: 2.75rem 2.25rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            box-shadow: var(--mf-shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pricing-card:hover {
            transform: translateY(-8px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: var(--mf-shadow-lg);
        }

        .pricing-card.featured {
            border: 1px solid var(--mf-lime);
            box-shadow: var(--mf-shadow-neon);
            background: linear-gradient(180deg, #182642 0%, #0d1629 100%);
        }

        .badge-popular {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #00ff87 0%, #10b981 100%);
            color: #04140d;
            font-weight: 800;
            font-size: 0.75rem;
            padding: 0.4rem 1.15rem;
            border-radius: 2rem;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            box-shadow: 0 4px 15px rgba(0, 255, 135, 0.4);
        }

        .price-value {
            font-size: 3.25rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1;
            letter-spacing: -0.03em;
        }

        .price-period {
            color: var(--mf-text-muted);
            font-size: 0.95rem;
        }

        .feature-list-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.9rem;
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .feature-list-item i {
            color: var(--mf-lime);
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        /* Surface Boxes */
        .surface-box {
            background: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: var(--mf-radius-md);
        }

        /* Tactical Pitch Preview */
        .pitch-preview {
            background: radial-gradient(circle, #064e3b 0%, #032a20 100%);
            border: 2px solid rgba(0, 255, 135, 0.3);
            border-radius: var(--mf-radius-md);
            padding: 1.75rem 1rem;
            position: relative;
            box-shadow: inset 0 0 30px rgba(0, 0, 0, 0.5);
        }

        .pitch-line {
            border-color: rgba(255, 255, 255, 0.25) !important;
        }

        /* FAQ Accordion */
        .accordion-item {
            background-color: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: var(--mf-radius-md) !important;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: var(--mf-shadow-sm);
        }

        .accordion-button {
            background-color: var(--mf-card-bg);
            color: #ffffff;
            font-weight: 600;
            font-size: 1.05rem;
            padding: 1.35rem 1.6rem;
            box-shadow: none !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(0, 255, 135, 0.08);
            color: var(--mf-lime);
        }

        .accordion-button::after {
            filter: invert(1);
        }

        .accordion-body {
            color: var(--mf-text-muted);
            line-height: 1.75;
            padding: 1.35rem 1.6rem;
            border-top: 1px solid var(--mf-card-border);
        }

        /* Footer */
        .landing-footer {
            background: #05080f;
            border-top: 1px solid var(--mf-card-border);
            color: var(--mf-text-subtle);
        }

        .footer-link {
            color: var(--mf-text-muted);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: var(--mf-lime);
        }
    </style>
@endsection

@section('body')
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg landing-navbar py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/media/logos/logo-fantasy-nobg.png') }}" alt="MiFantasy Logo" height="38" class="me-2">
                <span class="fw-bold text-white fs-4 tracking-wide">Mi<span style="color: var(--mf-lime);">Fantasy</span></span>
                <span class="badge border text-white-50 ms-2 px-2 py-1 fs-8" style="background: rgba(255, 255, 255, 0.06); border-color: var(--mf-card-border) !important;">B2B SaaS</span>
            </a>

            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#landingNav" aria-controls="landingNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="landingNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link" href="#beneficios">Beneficios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#modalidades">Modalidades</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#precios">Precios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">Preguntas</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-mf-primary d-flex align-items-center gap-2">
                            <i class="bi bi-speedometer2"></i> Mi Panel
                        </a>
                        <form action="{{ url('/logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm px-3 py-2 rounded-3">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-link text-white text-decoration-none fw-semibold px-3">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-mf-primary d-flex align-items-center gap-2">
                            <i class="bi bi-trophy-fill"></i> Crear mi Liga
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section py-5">
        <div class="container py-lg-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill hero-badge mb-4">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <span>Software SaaS para Organizadores de Ligas Amateur</span>
                    </div>

                    <h1 class="hero-title mb-4">
                        Convierte tu Liga Amateur en una Experiencia <span class="text-gradient">Fantasy Profesional</span>
                    </h1>

                    <p class="lead mb-4 fs-5" style="color: var(--mf-text-muted); line-height: 1.7;">
                        La plataforma multi-inquilino que automatiza la gestión deportiva, actas de partidos y alineaciones virtuales en <strong>Fútbol 11</strong>, <strong>Fútbol 7</strong> y <strong>Fútbol Sala</strong>.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start mb-5">
                        <a href="{{ route('register') }}" class="btn btn-mf-primary btn-lg gap-2">
                            <i class="bi bi-rocket-takeoff-fill"></i> Empezar 14 Días Gratis
                        </a>
                        <a href="#precios" class="btn btn-mf-outline btn-lg gap-2">
                            <i class="bi bi-tag-fill"></i> Ver Planes B2B
                        </a>
                    </div>

                    <!-- Trust Stats -->
                    <div class="row pt-4 border-top g-4 text-center text-lg-start" style="border-color: var(--mf-card-border) !important;">
                        <div class="col-4">
                            <div class="fw-bold fs-3" style="color: var(--mf-lime);">100%</div>
                            <small style="color: var(--mf-text-subtle);">Entorno Privado y Aislado</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold fs-3" style="color: var(--mf-cyan);">F11 / F7 / Sala</div>
                            <small style="color: var(--mf-text-subtle);">Motor Táctico en Vivo</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-white fs-3">Stripe</div>
                            <small style="color: var(--mf-text-subtle);">Cobros Automáticos</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mockup-container">
                        <div class="mockup-header">
                            <span class="mockup-dot bg-danger"></span>
                            <span class="mockup-dot bg-warning"></span>
                            <span class="mockup-dot bg-success"></span>
                            <span class="text-white-50 ms-2 small"><i class="bi bi-shield-lock-fill me-1"></i> panel.mifantasy.com/liga-amateur-pro</span>
                        </div>
                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="text-white mb-0 fw-bold">Liga Amateur San Mamés 2026</h6>
                                    <small style="color: var(--mf-lime);"><i class="bi bi-circle-fill fs-9"></i> Jornada 4 en curso - 14 Equipos</small>
                                </div>
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: rgba(56, 189, 248, 0.2); color: var(--mf-cyan); border: 1px solid rgba(56, 189, 248, 0.3);">Modalidad 11</span>
                            </div>

                            <!-- Pizarra táctica de muestra -->
                            <div class="pitch-preview text-center py-4 mb-3">
                                <div class="row g-2 justify-content-center mb-3">
                                    <div class="col-auto">
                                        <div class="badge bg-warning text-dark px-3 py-2 shadow-sm rounded-pill fw-bold"><i class="bi bi-person-fill"></i> Delantero (DC) · 14 pts</div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge bg-warning text-dark px-3 py-2 shadow-sm rounded-pill fw-bold"><i class="bi bi-person-fill"></i> Extremo (EI) · 9 pts</div>
                                    </div>
                                </div>
                                <div class="row g-2 justify-content-center mb-3">
                                    <div class="col-auto">
                                        <div class="badge bg-info text-dark px-3 py-2 shadow-sm rounded-pill fw-bold"><i class="bi bi-person-fill"></i> Centrocampista (MC) · 8 pts</div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge bg-info text-dark px-3 py-2 shadow-sm rounded-pill fw-bold"><i class="bi bi-person-fill"></i> Mediapunta (MCO) · 12 pts</div>
                                    </div>
                                </div>
                                <div class="row g-2 justify-content-center">
                                    <div class="col-auto">
                                        <div class="badge px-3 py-2 shadow-sm rounded-pill fw-bold" style="background: linear-gradient(135deg, #00ff87 0%, #10b981 100%); color: #04140d;"><i class="bi bi-person-fill"></i> Portero (POR) · 7 pts</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 text-start small">
                                <div class="col-6">
                                    <div class="p-3 rounded-3 surface-box">
                                        <span class="d-block" style="color: var(--mf-text-subtle);">Puntuación Total</span>
                                        <strong class="text-white fs-6">84 Puntos</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-3 surface-box">
                                        <span class="d-block" style="color: var(--mf-text-subtle);">Posición Global</span>
                                        <strong class="fs-6" style="color: var(--mf-lime);">#1 en la Liguilla</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Beneficios Section -->
    <section id="beneficios" class="py-5">
        <div class="container py-lg-5">
            <div class="text-center max-w-3xl mx-auto mb-5">
                <span class="fw-semibold text-uppercase tracking-wider small" style="color: var(--mf-lime);">Potencia tu Organización</span>
                <h2 class="display-6 fw-bold text-white mt-2 mb-3">Diseñado a Medida para Organizadores y Clubes</h2>
                <p class="fs-6 mx-auto" style="max-width: 650px; color: var(--mf-text-muted);">
                    Todo lo que necesitas para fidelizar a los jugadores de tus torneos con la emoción del formato Fantasy en tiempo real.
                </p>
            </div>

            <div class="row g-4">
                <!-- Beneficio 1: Multi-Tenancy -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper" style="background: rgba(0, 255, 135, 0.12); color: var(--mf-lime);">
                            <i class="bi bi-buildings-fill"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-3">Entorno Privado y Aislado</h4>
                        <p class="mb-4" style="color: var(--mf-text-muted); line-height: 1.65;">
                            Arquitectura Multi-Tenant avanzada. Cada liga disfruta de un espacio independiente, datos blindados, configuración de equipos personalizada y branding exclusivo.
                        </p>
                        <ul class="list-unstyled mb-0 small" style="color: var(--mf-text-muted);">
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--mf-lime);"></i> Base de datos y alcance por inquilino</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--mf-lime);"></i> Privacidad total para tus participantes</li>
                            <li><i class="bi bi-check-circle-fill me-2" style="color: var(--mf-lime);"></i> Escalabilidad sin interferencias</li>
                        </ul>
                    </div>
                </div>

                <!-- Beneficio 2: Panel de Gestión -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper" style="background: rgba(56, 189, 248, 0.12); color: var(--mf-cyan);">
                            <i class="bi bi-sliders2-vertical"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-3">Panel de Gestión Propio</h4>
                        <p class="mb-4" style="color: var(--mf-text-muted); line-height: 1.65;">
                            Controla calendarios, plantillas, resultados y actas de partido en segundos. Asigna permisos y delega tareas a árbitros y delegados con roles Spatie Teams.
                        </p>
                        <ul class="list-unstyled mb-0 small" style="color: var(--mf-text-muted);">
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--mf-cyan);"></i> Gestión de torneos y jornadas</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--mf-cyan);"></i> Roles de administrador local por liga</li>
                            <li><i class="bi bi-check-circle-fill me-2" style="color: var(--mf-cyan);"></i> Registro rápido de goles y tarjetas</li>
                        </ul>
                    </div>
                </div>

                <!-- Beneficio 3: Motor de Alineaciones -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper" style="background: rgba(168, 85, 247, 0.12); color: #c084fc;">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-3">Motor Táctico 11 / 7 / Sala</h4>
                        <p class="mb-4" style="color: var(--mf-text-muted); line-height: 1.65;">
                            Tus jugadores pueden elegir formaciones interactivas (4-3-3, 4-4-2, 3-2-1, 2-2, 1-2-1), alinear sus futbolistas favoritos y sumar puntos tras cada jornada oficial.
                        </p>
                        <ul class="list-unstyled mb-0 small" style="color: var(--mf-text-muted);">
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #c084fc;"></i> Adaptable a cualquier modalidad</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #c084fc;"></i> Puntuación automática tras el acta</li>
                            <li><i class="bi bi-check-circle-fill me-2" style="color: #c084fc;"></i> Clasificación general y por jornada</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modalidades Showcase -->
    <section id="modalidades" class="py-5" style="background: var(--mf-bg-surface); border-top: 1px solid var(--mf-card-border); border-bottom: 1px solid var(--mf-card-border);">
        <div class="container py-lg-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="fw-semibold text-uppercase tracking-wider small" style="color: var(--mf-lime);">Flexibilidad Total</span>
                    <h2 class="display-6 fw-bold text-white mt-2 mb-4">Soporte Nativo para Todas las Modalidades</h2>
                    <p class="mb-4" style="color: var(--mf-text-muted);">
                        Ya organices un torneo nocturno de Fútbol Sala, una liga de empresas de Fútbol 7 o un campeonato regional de Fútbol 11, MiFantasy adapta automáticamente las plantillas y el tablero táctico.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 surface-box">
                            <div class="fw-bold rounded-circle p-2 px-3 fs-5" style="background: var(--mf-lime); color: #04140d;">11</div>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Fútbol 11 Clásico</h6>
                                <p class="small mb-0" style="color: var(--mf-text-muted);">Formaciones 4-3-3, 4-4-2, 3-5-2 y 5-3-2 con portero, defensas, medios y delanteros.</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 surface-box">
                            <div class="fw-bold rounded-circle p-2 px-3 fs-5" style="background: var(--mf-cyan); color: #04140d;">7</div>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Fútbol 7 Dinámico</h6>
                                <p class="small mb-0" style="color: var(--mf-text-muted);">Formaciones tácticas 3-2-1, 2-3-1 y 3-1-2 optimizadas para campos reducidos.</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 surface-box">
                            <div class="fw-bold rounded-circle p-2 px-3 fs-5" style="background: #fbbf24; color: #04140d;">5</div>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Fútbol Sala</h6>
                                <p class="small mb-0" style="color: var(--mf-text-muted);">Sistemas de juego 1-2-1 (Rombo), 2-2 (Cuadrado) y 3-1 con rotación ágil.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <div class="p-4 rounded-4 surface-box shadow-lg">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="text-white mb-0 fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i> Clasificación en Tiempo Real</h5>
                            <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: rgba(0, 255, 135, 0.15); color: var(--mf-lime); border: 1px solid rgba(0, 255, 135, 0.3);">Actualizado</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-start mb-0" style="color: var(--mf-text-muted);">
                                <thead>
                                    <tr class="small" style="border-bottom: 1px solid var(--mf-card-border); color: var(--mf-text-subtle);">
                                        <th>Pos</th>
                                        <th>Manager / Equipo</th>
                                        <th>Jornada</th>
                                        <th>Total Pts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom: 1px solid var(--mf-card-border);">
                                        <td class="fw-bold text-warning">#1</td>
                                        <td><strong class="text-white">Galácticos FC</strong><br><small style="color: var(--mf-text-subtle);">Carlos Ruiz</small></td>
                                        <td><span class="badge fw-bold" style="background: rgba(0, 255, 135, 0.15); color: var(--mf-lime);">+78</span></td>
                                        <td class="fw-bold text-white fs-6">312</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid var(--mf-card-border);">
                                        <td class="fw-bold" style="color: var(--mf-text-subtle);">#2</td>
                                        <td><strong class="text-white">Fútbol Club Ronin</strong><br><small style="color: var(--mf-text-subtle);">Laura Gómez</small></td>
                                        <td><span class="badge fw-bold" style="background: rgba(0, 255, 135, 0.15); color: var(--mf-lime);">+65</span></td>
                                        <td class="fw-bold text-white fs-6">298</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold" style="color: var(--mf-text-subtle);">#3</td>
                                        <td><strong class="text-white">Rayo Bohemio</strong><br><small style="color: var(--mf-text-subtle);">David Vidal</small></td>
                                        <td><span class="badge fw-bold" style="background: rgba(0, 255, 135, 0.15); color: var(--mf-lime);">+71</span></td>
                                        <td class="fw-bold text-white fs-6">289</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Precios Section (Stripe B2B Ready) -->
    <section id="precios" class="py-5">
        <div class="container py-lg-5">
            <div class="text-center max-w-3xl mx-auto mb-5">
                <span class="fw-semibold text-uppercase tracking-wider small" style="color: var(--mf-lime);">Precios B2B Transparentes</span>
                <h2 class="display-6 fw-bold text-white mt-2 mb-3">Planes Listos para Escalar tu Organización</h2>
                <p class="fs-6 mx-auto" style="max-width: 650px; color: var(--mf-text-muted);">
                    Facturación mensual o por torneo integrada con Stripe. Sin costes ocultos ni comisiones por jugador.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Plan Básico -->
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-white fw-bold mb-0">Plan Básico</h4>
                                <span class="badge px-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.08); color: var(--mf-text-muted);">Starter</span>
                            </div>
                            <p class="small mb-4" style="color: var(--mf-text-muted);">Ideal para ligas de barrio, torneos de verano o pequeñas asociaciones.</p>
                            <div class="d-flex align-items-baseline gap-2 mb-4">
                                <span class="price-value">29€</span>
                                <span class="price-period">/ mes</span>
                            </div>

                            <div class="border-top pt-4 mb-4" style="border-color: var(--mf-card-border) !important;">
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Hasta <strong>1 Torneo</strong> activo</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Hasta <strong>12 Equipos</strong> oficiales</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Motor <strong>Fútbol 11, 7 y Sala</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>1 Administrador de Liga</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Soporte por email en 48h</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register', ['plan' => 'basico']) }}" class="btn btn-mf-outline w-100 py-3 fw-semibold">
                            Elegir Plan Básico
                        </a>
                    </div>
                </div>

                <!-- Plan Pro (Destacado) -->
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card featured">
                        <span class="badge-popular">Más Popular</span>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                                <h4 class="text-white fw-bold mb-0">Plan Pro</h4>
                                <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: var(--mf-lime); color: #04140d;">Organizador</span>
                            </div>
                            <p class="small mb-4" style="color: #cbd5e1;">La solución integral para ligas consolidadas y clubes deportivos.</p>
                            <div class="d-flex align-items-baseline gap-2 mb-4">
                                <span class="price-value">79€</span>
                                <span class="price-period" style="color: #cbd5e1;">/ mes</span>
                            </div>

                            <div class="border-top pt-4 mb-4" style="border-color: rgba(255, 255, 255, 0.12) !important;">
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Torneos y Liguillas <strong>Ilimitadas</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Equipos y Jugadores <strong>Ilimitados</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Entorno <strong>Multi-Tenant Blindado</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Roles de delegados y árbitros (Spatie)</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Cobros y Suscripciones automáticas</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Soporte Prioritario 24/7</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register', ['plan' => 'pro']) }}" class="btn btn-mf-primary w-100 py-3 fw-semibold">
                            Empezar 14 Días Gratis
                        </a>
                    </div>
                </div>

                <!-- Plan Premium / Enterprise -->
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-white fw-bold mb-0">Plan Enterprise</h4>
                                <span class="badge px-3 py-1 rounded-pill fw-bold" style="background: rgba(56, 189, 248, 0.15); color: var(--mf-cyan); border: 1px solid rgba(56, 189, 248, 0.3);">Federaciones</span>
                            </div>
                            <p class="small mb-4" style="color: var(--mf-text-muted);">Para macro-complejos deportivos, federaciones y múltiples sedes.</p>
                            <div class="d-flex align-items-baseline gap-2 mb-4">
                                <span class="price-value">199€</span>
                                <span class="price-period">/ mes</span>
                            </div>

                            <div class="border-top pt-4 mb-4" style="border-color: var(--mf-card-border) !important;">
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Todo lo incluido en el <strong>Plan Pro</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Subdominio y <strong>Marca Blanca</strong> 100%</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Acceso a <strong>API REST</strong> & Webhooks</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>Onboarding personalizado y migración</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2"></i>
                                    <span>SLA de Disponibilidad 99.9%</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register', ['plan' => 'enterprise']) }}" class="btn btn-mf-outline w-100 py-3 fw-semibold">
                            Contactar con Ventas
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 small" style="color: var(--mf-text-subtle);">
                <i class="bi bi-shield-check me-1" style="color: var(--mf-lime);"></i> Facturación segura cifrada vía <strong>Stripe Billing</strong>. Cancela en cualquier momento sin penalizaciones.
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5" style="background: var(--mf-bg-surface); border-top: 1px solid var(--mf-card-border);">
        <div class="container py-lg-4" style="max-width: 850px;">
            <div class="text-center mb-5">
                <span class="fw-semibold text-uppercase tracking-wider small" style="color: var(--mf-lime);">Resolvemos tus dudas</span>
                <h2 class="display-6 fw-bold text-white mt-2">Preguntas Frecuentes</h2>
            </div>

            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                            ¿Cómo funciona la arquitectura Multi-Tenant de MiFantasy?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Cada organizador obtiene un espacio de inquilino (Tenant) aislado. Esto asegura que tus torneos, equipos, jugadores y liguillas privadas nunca se mezclen con los de otras organizaciones, permitiéndote además delegar roles de administración local sin comprometer datos globales.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ¿Qué modalidades deportivas son compatibles?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Soportamos nativamente Fútbol 11 (11 jugadores por alineación), Fútbol 7 (7 jugadores) y Fútbol Sala (5 jugadores). El sistema adapta automáticamente el tablero táctico, las posiciones válidas (Portero, Defensa, Medio, Delantero) y las formaciones permitidas.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            ¿Cómo se calculan las puntuaciones de los jugadores?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Al finalizar cada partido oficial de tu torneo, el administrador o árbitro ingresa las estadísticas del acta (goles marcados, tarjetas amarillas/rojas, titularidad o goles encajados). El motor de MiFantasy recalcula automáticamente en segundo plano los puntos de cada participante en la liguilla.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ¿Puedo cobrar cuotas o suscripciones a mis participantes con Stripe?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Sí. Gracias a la integración con Laravel Cashier y Stripe, los organizadores en los planes Pro y Enterprise pueden configurar cobros automatizados para la inscripción en ligas y torneos.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="py-5 text-center position-relative">
        <div class="container py-lg-5">
            <div class="p-5 rounded-4" style="background: radial-gradient(circle at center, rgba(0, 255, 135, 0.15) 0%, rgba(17, 26, 46, 0.95) 100%); border: 1px solid var(--mf-card-border); box-shadow: var(--mf-shadow-lg);">
                <h2 class="display-6 fw-bold text-white mb-3">¿Listo para llevar tu liga amateur al siguiente nivel?</h2>
                <p class="fs-5 mb-4 mx-auto" style="max-width: 600px; color: var(--mf-text-muted);">
                    Únete a los organizadores que ya han transformado sus torneos en una experiencia inolvidable.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ route('register') }}" class="btn btn-mf-primary btn-lg px-5 py-3">
                        <i class="bi bi-trophy-fill me-2"></i> Crear mi Liga Gratis
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-mf-outline btn-lg px-4 py-3">
                        Acceso a Mi Cuenta
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Moderno -->
    <footer class="landing-footer py-5">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-lg-4">
                    <a class="d-flex align-items-center text-decoration-none mb-3" href="{{ url('/') }}">
                        <img src="{{ asset('assets/media/logos/logo-fantasy-nobg.png') }}" alt="MiFantasy Logo" height="32" class="me-2">
                        <span class="fw-bold text-white fs-5">Mi<span style="color: var(--mf-lime);">Fantasy</span></span>
                    </a>
                    <p class="small mb-3" style="color: var(--mf-text-subtle); line-height: 1.65;">
                        La plataforma SaaS B2B líder para la gestión y digitalización de ligas y torneos deportivos amateur con formato Fantasy en tiempo real.
                    </p>
                    <div class="d-flex gap-3 fs-5">
                        <a href="#" class="footer-link"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="footer-link"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="footer-link"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="footer-link"><i class="bi bi-github"></i></a>
                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">Producto</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="#beneficios" class="footer-link">Beneficios</a></li>
                        <li><a href="#modalidades" class="footer-link">Modalidades 11/7/Sala</a></li>
                        <li><a href="#precios" class="footer-link">Planes B2B</a></li>
                        <li><a href="#faq" class="footer-link">Preguntas Frecuentes</a></li>
                    </ul>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">Legal</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="#" class="footer-link">Términos de Servicio</a></li>
                        <li><a href="#" class="footer-link">Política de Privacidad</a></li>
                        <li><a href="#" class="footer-link">Cookies y RGPD</a></li>
                        <li><a href="#" class="footer-link">Seguridad Multi-Tenant</a></li>
                    </ul>
                </div>

                <div class="col-md-4 col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Soporte & Contacto</h6>
                    <p class="small mb-2" style="color: var(--mf-text-muted);"><i class="bi bi-envelope-fill me-2" style="color: var(--mf-lime);"></i> soporte@mifantasy.com</p>
                    <p class="small mb-3" style="color: var(--mf-text-muted);"><i class="bi bi-shield-check me-2" style="color: var(--mf-lime);"></i> Pagos Seguros con Stripe</p>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-mf-outline w-100 py-2">
                        Registrar Organizador
                    </a>
                </div>
            </div>

            <div class="mt-5 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 small" style="border-top: 1px solid var(--mf-card-border); color: var(--mf-text-subtle);">
                <span>© {{ date('Y') }} MiFantasy B2B SaaS. Todos los derechos reservados.</span>
                <span>Desarrollado para optimizar competiciones deportivas.</span>
            </div>
        </div>
    </footer>
@endsection
