@extends('layouts.base')

@section('head')
    <title>MiFantasy - La Plataforma SaaS B2B para Ligas y Torneos Amateur</title>
    <meta name="description" content="Gestiona y profesionaliza tus torneos de Fútbol 11, Fútbol 7 y Fútbol Sala con MiFantasy. Software SaaS multi-tenant con motor de alineaciones y puntuaciones automáticas.">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos base de la aplicación -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/fantasy.css') }}" rel="stylesheet" />

    <style>
        :root {
            --mf-bg-dark: #0f172a;
            --mf-card-bg: #1e293b;
            --mf-card-border: rgba(255, 255, 255, 0.08);
            --mf-primary: #10b981;
            --mf-primary-hover: #059669;
            --mf-accent-blue: #3b82f6;
        }

        body {
            background-color: var(--mf-bg-dark);
            color: #e2e8f0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* Navbar */
        .landing-navbar {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--mf-card-border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-link {
            color: #94a3b8 !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: #ffffff !important;
        }

        /* Hero */
        .hero-section {
            position: relative;
            padding-top: 5rem;
            padding-bottom: 5rem;
            background: radial-gradient(circle at 50% 20%, rgba(16, 185, 129, 0.12) 0%, rgba(15, 23, 42, 0) 70%);
        }

        .hero-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 800;
            line-height: 1.15;
            color: #ffffff;
        }

        .text-gradient {
            background: linear-gradient(135deg, #10b981 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-mf-primary {
            background-color: var(--mf-primary);
            border-color: var(--mf-primary);
            color: #ffffff;
            font-weight: 600;
            padding: 0.8rem 1.8rem;
            border-radius: 0.75rem;
            transition: all 0.25s ease;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        }

        .btn-mf-primary:hover {
            background-color: var(--mf-primary-hover);
            border-color: var(--mf-primary-hover);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.5);
        }

        .btn-mf-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 600;
            padding: 0.8rem 1.8rem;
            border-radius: 0.75rem;
            transition: all 0.25s ease;
        }

        .btn-mf-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.4);
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* Mockup Preview */
        .mockup-container {
            background: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            overflow: hidden;
            position: relative;
        }

        .mockup-header {
            background: rgba(15, 23, 42, 0.8);
            padding: 0.85rem 1.25rem;
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

        /* Cards & Features */
        .feature-card {
            background: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: 1.25rem;
            padding: 2.25rem;
            height: 100%;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.5);
        }

        .feature-icon-wrapper {
            width: 58px;
            height: 58px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
        }

        /* Pricing */
        .pricing-card {
            background: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-6px);
        }

        .pricing-card.featured {
            border-color: var(--mf-primary);
            box-shadow: 0 0 35px rgba(16, 185, 129, 0.2);
            background: linear-gradient(180deg, #1e293b 0%, #172554 100%);
        }

        .badge-popular {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--mf-primary);
            color: #0f172a;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.35rem 1rem;
            border-radius: 2rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .price-value {
            font-size: 3rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
        }

        .price-period {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .feature-list-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .feature-list-item i {
            color: var(--mf-primary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* FAQ Accordion */
        .accordion-item {
            background-color: var(--mf-card-bg);
            border: 1px solid var(--mf-card-border);
            border-radius: 0.75rem !important;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .accordion-button {
            background-color: var(--mf-card-bg);
            color: #ffffff;
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            box-shadow: none !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(16, 185, 129, 0.08);
            color: #34d399;
        }

        .accordion-button::after {
            filter: invert(1);
        }

        .accordion-body {
            color: #94a3b8;
            line-height: 1.7;
            padding: 1.25rem 1.5rem;
        }

        /* Tactical Pitch Preview */
        .pitch-preview {
            background: radial-gradient(circle, #065f46 0%, #064e3b 100%);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.85rem;
            padding: 1.5rem;
            position: relative;
        }

        .pitch-line {
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        /* Footer */
        .landing-footer {
            background: #090d16;
            border-top: 1px solid var(--mf-card-border);
            color: #64748b;
        }

        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: #ffffff;
        }
    </style>
@endsection

@section('body')
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg landing-navbar py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/media/logos/logo-fantasy-nobg.png') }}" alt="MiFantasy Logo" height="38" class="me-2">
                <span class="fw-bold text-white fs-4 tracking-wide">Mi<span class="text-success">Fantasy</span></span>
                <span class="badge bg-dark border border-secondary text-white-50 ms-2 px-2 py-1 fs-8">B2B SaaS</span>
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

                    <p class="lead text-secondary mb-4 fs-5" style="line-height: 1.7;">
                        La plataforma multi-inquilino que automatiza la gestión deportiva, actas de partidos y alineaciones virtuales en <strong>Fútbol 11</strong>, <strong>Fútbol 7</strong> y <strong>Fútbol Sala</strong>.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start mb-5">
                        <a href="{{ route('register') }}" class="btn btn-mf-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-rocket-takeoff-fill"></i> Empezar 14 Días Gratis
                        </a>
                        <a href="#precios" class="btn btn-mf-outline btn-lg d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-tag-fill"></i> Ver Planes B2B
                        </a>
                    </div>

                    <!-- Trust Stats -->
                    <div class="row pt-4 border-top border-secondary border-opacity-25 g-4 text-center text-lg-start">
                        <div class="col-4">
                            <div class="fw-bold text-white fs-3">100%</div>
                            <small class="text-secondary">Multi-Tenant Aislado</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-white fs-3">F11 / F7 / Sala</div>
                            <small class="text-secondary">Puntuaciones en Vivo</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-white fs-3">Stripe</div>
                            <small class="text-secondary">Cobros Automáticos</small>
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
                                    <small class="text-success"><i class="bi bi-circle-fill fs-9"></i> Jornada 4 en curso - 14 Equipos</small>
                                </div>
                                <span class="badge bg-primary px-3 py-2 rounded-pill">Modalidad 11</span>
                            </div>

                            <!-- Pizarra táctica de muestra -->
                            <div class="pitch-preview text-center py-4 mb-3">
                                <div class="row g-2 justify-content-center mb-3">
                                    <div class="col-auto">
                                        <div class="badge bg-warning text-dark px-3 py-2 shadow-sm rounded-pill"><i class="bi bi-person-fill"></i> Delantero (DC) · 14 pts</div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge bg-warning text-dark px-3 py-2 shadow-sm rounded-pill"><i class="bi bi-person-fill"></i> Extremo (EI) · 9 pts</div>
                                    </div>
                                </div>
                                <div class="row g-2 justify-content-center mb-3">
                                    <div class="col-auto">
                                        <div class="badge bg-info text-dark px-3 py-2 shadow-sm rounded-pill"><i class="bi bi-person-fill"></i> Centrocampista (MC) · 8 pts</div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="badge bg-info text-dark px-3 py-2 shadow-sm rounded-pill"><i class="bi bi-person-fill"></i> Mediapunta (MCO) · 12 pts</div>
                                    </div>
                                </div>
                                <div class="row g-2 justify-content-center">
                                    <div class="col-auto">
                                        <div class="badge bg-primary text-white px-3 py-2 shadow-sm rounded-pill"><i class="bi bi-person-fill"></i> Portero (POR) · 7 pts</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 text-start small">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-dark border border-secondary border-opacity-25">
                                        <span class="text-secondary d-block">Puntuación Total</span>
                                        <strong class="text-white fs-6">84 Puntos</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-dark border border-secondary border-opacity-25">
                                        <span class="text-secondary d-block">Posición Global</span>
                                        <strong class="text-success fs-6">#1 en la Liguilla</strong>
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
                <span class="text-success fw-semibold text-uppercase tracking-wider small">Potencia tu Organización</span>
                <h2 class="display-6 fw-bold text-white mt-2 mb-3">Diseñado a Medida para Organizadores y Clubes</h2>
                <p class="text-secondary fs-6 mx-auto" style="max-width: 650px;">
                    Todo lo que necesitas para fidelizar a los jugadores de tus torneos con la emoción del formato Fantasy en tiempo real.
                </p>
            </div>

            <div class="row g-4">
                <!-- Beneficio 1: Multi-Tenancy -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-success bg-opacity-10 text-success">
                            <i class="bi bi-buildings-fill"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-3">Entorno Privado y Aislado</h4>
                        <p class="text-secondary mb-4" style="line-height: 1.6;">
                            Arquitectura Multi-Tenant avanzada. Cada liga disfruta de un espacio independiente, datos blindados, configuración de equipos personalizada y branding exclusivo.
                        </p>
                        <ul class="list-unstyled mb-0 small text-secondary">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Base de datos y alcance por inquilino</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Privacidad total para tus participantes</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i> Escalabilidad sin interferencias</li>
                        </ul>
                    </div>
                </div>

                <!-- Beneficio 2: Panel de Gestión -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-sliders2-vertical"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-3">Panel de Gestión Propio</h4>
                        <p class="text-secondary mb-4" style="line-height: 1.6;">
                            Controla calendarios, plantillas, resultados y actas de partido en segundos. Asigna permisos y delega tareas a árbitros y delegados con roles Spatie Teams.
                        </p>
                        <ul class="list-unstyled mb-0 small text-secondary">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Gestión de torneos y jornadas</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Roles de administrador local por liga</li>
                            <li><i class="bi bi-check-circle-fill text-primary me-2"></i> Registro rápido de goles y tarjetas</li>
                        </ul>
                    </div>
                </div>

                <!-- Beneficio 3: Motor de Alineaciones -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper bg-info bg-opacity-10 text-info">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-3">Motor Táctico 11 / 7 / Sala</h4>
                        <p class="text-secondary mb-4" style="line-height: 1.6;">
                            Tus jugadores pueden elegir formaciones interactivas (4-3-3, 4-4-2, 3-2-1, 2-2, 1-2-1), alinear sus futbolistas favoritos y sumar puntos tras cada jornada oficial.
                        </p>
                        <ul class="list-unstyled mb-0 small text-secondary">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-info me-2"></i> Adaptable a cualquier modalidad</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-info me-2"></i> Puntuación automática tras el acta</li>
                            <li><i class="bi bi-check-circle-fill text-info me-2"></i> Clasificación general y por jornada</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modalidades Showcase -->
    <section id="modalidades" class="py-5 bg-dark bg-opacity-50 border-top border-bottom border-secondary border-opacity-10">
        <div class="container py-lg-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="text-success fw-semibold text-uppercase tracking-wider small">Flexibilidad Total</span>
                    <h2 class="display-6 fw-bold text-white mt-2 mb-4">Soporte Nativo para Todas las Modalidades</h2>
                    <p class="text-secondary mb-4">
                        Ya organices un torneo nocturno de Fútbol Sala, una liga de empresas de Fútbol 7 o un campeonato regional de Fútbol 11, MiFantasy adapta automáticamente las plantillas y el tablero táctico.
                    </p>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-dark border border-secondary border-opacity-25">
                            <div class="bg-success text-dark fw-bold rounded-circle p-2 px-3 fs-5">11</div>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Fútbol 11 Clásico</h6>
                                <p class="text-secondary small mb-0">Formaciones 4-3-3, 4-4-2, 3-5-2 y 5-3-2 con portero, defensas, medios y delanteros.</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-dark border border-secondary border-opacity-25">
                            <div class="bg-primary text-white fw-bold rounded-circle p-2 px-3 fs-5">7</div>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Fútbol 7 Dinámico</h6>
                                <p class="text-secondary small mb-0">Formaciones tácticas 3-2-1, 2-3-1 y 3-1-2 optimizadas para campos reducidos.</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-dark border border-secondary border-opacity-25">
                            <div class="bg-warning text-dark fw-bold rounded-circle p-2 px-3 fs-5">5</div>
                            <div>
                                <h6 class="text-white fw-bold mb-1">Fútbol Sala</h6>
                                <p class="text-secondary small mb-0">Sistemas de juego 1-2-1 (Rombo), 2-2 (Cuadrado) y 3-1 con rotación ágil.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <div class="p-4 rounded-4 bg-dark border border-secondary border-opacity-25 shadow-lg">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="text-white mb-0 fw-bold"><i class="bi bi-trophy text-warning me-2"></i> Clasificación en Tiempo Real</h5>
                            <span class="badge bg-success">Actualizado</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle text-start mb-0">
                                <thead>
                                    <tr class="text-secondary small">
                                        <th>Pos</th>
                                        <th>Manager / Equipo</th>
                                        <th>Jornada</th>
                                        <th>Total Pts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold text-warning">#1</td>
                                        <td><strong class="text-white">Galácticos FC</strong><br><small class="text-secondary">Carlos Ruiz</small></td>
                                        <td><span class="badge bg-success bg-opacity-25 text-success">+78</span></td>
                                        <td class="fw-bold text-white fs-6">312</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-white-50">#2</td>
                                        <td><strong class="text-white">Fútbol Club Ronin</strong><br><small class="text-secondary">Laura Gómez</small></td>
                                        <td><span class="badge bg-success bg-opacity-25 text-success">+65</span></td>
                                        <td class="fw-bold text-white fs-6">298</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-white-50">#3</td>
                                        <td><strong class="text-white">Rayo Bohemio</strong><br><small class="text-secondary">David Vidal</small></td>
                                        <td><span class="badge bg-success bg-opacity-25 text-success">+71</span></td>
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
                <span class="text-success fw-semibold text-uppercase tracking-wider small">Precios B2B Transparentes</span>
                <h2 class="display-6 fw-bold text-white mt-2 mb-3">Planes Listos para Escalar tu Organización</h2>
                <p class="text-secondary fs-6 mx-auto" style="max-width: 650px;">
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
                                <span class="badge bg-secondary bg-opacity-25 text-white-50 px-2 py-1">Starter</span>
                            </div>
                            <p class="text-secondary small mb-4">Ideal para ligas de barrio, torneos de verano o pequeñas asociaciones.</p>
                            <div class="d-flex align-items-baseline gap-2 mb-4">
                                <span class="price-value">29€</span>
                                <span class="price-period">/ mes</span>
                            </div>

                            <div class="border-top border-secondary border-opacity-25 pt-4 mb-4">
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
                                <span class="badge bg-success text-dark px-2 py-1 fw-bold">Organizador</span>
                            </div>
                            <p class="text-secondary small mb-4">La solución integral para ligas consolidadas y clubes deportivos.</p>
                            <div class="d-flex align-items-baseline gap-2 mb-4">
                                <span class="price-value">79€</span>
                                <span class="price-period">/ mes</span>
                            </div>

                            <div class="border-top border-secondary border-opacity-25 pt-4 mb-4">
                                <div class="feature-list-item">
                                    <i class="bi bi-check2 text-success"></i>
                                    <span>Torneos y Liguillas <strong>Ilimitadas</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2 text-success"></i>
                                    <span>Equipos y Jugadores <strong>Ilimitados</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2 text-success"></i>
                                    <span>Entorno <strong>Multi-Tenant Blindado</strong></span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2 text-success"></i>
                                    <span>Roles de delegados y árbitros (Spatie)</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2 text-success"></i>
                                    <span>Cobros y Suscripciones automáticas</span>
                                </div>
                                <div class="feature-list-item">
                                    <i class="bi bi-check2 text-success"></i>
                                    <span>Soporte Prioritario 24/7</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register', ['plan' => 'pro']) }}" class="btn btn-mf-primary w-100 py-3 fw-semibold shadow">
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
                                <span class="badge bg-info bg-opacity-25 text-info px-2 py-1">Federaciones</span>
                            </div>
                            <p class="text-secondary small mb-4">Para macro-complejos deportivos, federaciones y múltiples sedes.</p>
                            <div class="d-flex align-items-baseline gap-2 mb-4">
                                <span class="price-value">199€</span>
                                <span class="price-period">/ mes</span>
                            </div>

                            <div class="border-top border-secondary border-opacity-25 pt-4 mb-4">
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

            <div class="text-center mt-5 text-secondary small">
                <i class="bi bi-shield-check text-success me-1"></i> Facturación segura cifrada vía <strong>Stripe Billing</strong>. Cancela en cualquier momento sin penalizaciones.
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 bg-dark bg-opacity-30">
        <div class="container py-lg-4 max-w-4xl" style="max-width: 850px;">
            <div class="text-center mb-5">
                <span class="text-success fw-semibold text-uppercase tracking-wider small">Resolvemos tus dudas</span>
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
            <div class="p-5 rounded-4 bg-gradient" style="background: radial-gradient(circle at center, rgba(16, 185, 129, 0.2) 0%, rgba(30, 41, 59, 0.95) 100%); border: 1px solid var(--mf-card-border);">
                <h2 class="display-6 fw-bold text-white mb-3">¿Listo para llevar tu liga amateur al siguiente nivel?</h2>
                <p class="text-secondary fs-5 mb-4 mx-auto" style="max-width: 600px;">
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
                        <span class="fw-bold text-white fs-5">Mi<span class="text-success">Fantasy</span></span>
                    </a>
                    <p class="text-secondary small mb-3" style="line-height: 1.6;">
                        La plataforma SaaS B2B líder para la gestión y digitalización de ligas y torneos deportivos amateur con formato Fantasy en tiempo real.
                    </p>
                    <div class="d-flex gap-3 text-secondary fs-5">
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
                    <p class="text-secondary small mb-2"><i class="bi bi-envelope-fill me-2 text-success"></i> soporte@mifantasy.com</p>
                    <p class="text-secondary small mb-3"><i class="bi bi-shield-check me-2 text-success"></i> Pagos Seguros con Stripe</p>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-light w-100 rounded-3">
                        Registrar Organizador
                    </a>
                </div>
            </div>

            <div class="border-top border-secondary border-opacity-25 mt-5 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 small">
                <span class="text-secondary">© {{ date('Y') }} MiFantasy B2B SaaS. Todos los derechos reservados.</span>
                <span class="text-secondary">Desarrollado para optimizar competiciones deportivas.</span>
            </div>
        </div>
    </footer>
@endsection
