![MiFantasy Logo](./public/assets/media/logos/logo-fantasy-pitch.svg)

[![GitHub Repo](https://img.shields.io/badge/-View%20GitHub-181717?style=flat&logo=github&labelColor=181717)](https://github.com/UrkiAristu/MiFantasy)
[![VS Code Web](https://img.shields.io/badge/-Open%20vscode.dev-9E95B7?style=flat&logo=refinedgithub&labelColor=181717)](https://github.dev/transper-dev/MiFantasy)
[![DeepWiki](https://img.shields.io/badge/-Ask%20DeepWiki-blue.svg?style=flat&labelColor=181717&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAyCAYAAAAnWDnqAAAAAXNSR0IArs4c6QAAA05JREFUaEPtmUtyEzEQhtWTQyQLHNak2AB7ZnyXZMEjXMGeK/AIi+QuHrMnbChYY7MIh8g01fJoopFb0uhhEqqcbWTp06/uv1saEDv4O3n3dV60RfP947Mm9/SQc0ICFQgzfc4CYZoTPAswgSJCCUJUnAAoRHOAUOcATwbmVLWdGoH//PB8mnKqScAhsD0kYP3j/Yt5LPQe2KvcXmGvRHcDnpxfL2zOYJ1mFwrryWTz0advv1Ut4CJgf5uhDuDj5eUcAUoahrdY/56ebRWeraTjMt/00Sh3UDtjgHtQNHwcRGOC98BJEAEymycmYcWwOprTgcB6VZ5JK5TAJ+fXGLBm3FDAmn6oPPjR4rKCAoJCal2eAiQp2x0vxTPB3ALO2CRkwmDy5WohzBDwSEFKRwPbknEggCPB/imwrycgxX2NzoMCHhPkDwqYMr9tRcP5qNrMZHkVnOjRMWwLCcr8ohBVb1OMjxLwGCvjTikrsBOiA6fNyCrm8V1rP93iVPpwaE+gO0SsWmPiXB+jikdf6SizrT5qKasx5j8ABbHpFTx+vFXp9EnYQmLx02h1QTTrl6eDqxLnGjporxl3NL3agEvXdT0WmEost648sQOYAeJS9Q7bfUVoMGnjo4AZdUMQku50McDcMWcBPvr0SzbTAFDfvJqwLzgxwATnCgnp4wDl6Aa+Ax283gghmj+vj7feE2KBBRMW3FzOpLOADl0Isb5587h/U4gGvkt5v60Z1VLG8BhYjbzRwyQZemwAd6cCR5/XFWLYZRIMpX39AR0tjaGGiGzLVyhse5C9RKC6ai42ppWPKiBagOvaYk8lO7DajerabOZP46Lby5wKjw1HCRx7p9sVMOWGzb/vA1hwiWc6jm3MvQDTogQkiqIhJV0nBQBTU+3okKCFDy9WwferkHjtxib7t3xIUQtHxnIwtx4mpg26/HfwVNVDb4oI9RHmx5WGelRVlrtiw43zboCLaxv46AZeB3IlTkwouebTr1y2NjSpHz68WNFjHvupy3q8TFn3Hos2IAk4Ju5dCo8B3wP7VPr/FGaKiG+T+v+TQqIrOqMTL1VdWV1DdmcbO8KXBz6esmYWYKPwDL5b5FA1a0hwapHiom0r/cKaoqr+27/XcrS5UwSMbQAAAABJRU5ErkJggg==)](https://deepwiki.com/UrkiAristu/MiFantasy)
[![GitDiagram](https://img.shields.io/badge/-Review%20GitDiagram-F03C2E?style=flat&logo=git&labelColor=181717)](https://gitdiagram.com/UrkiAristu/MiFantasy)
[![GitIngest](https://img.shields.io/badge/-Extract%20GitIngest-FF6B6B?style=flat&logo=files&logoColor=FF6B6B&labelColor=181717)](https://gitingest.com/UrkiAristu/MiFantasy)

# Sobre MiFantasy

MiFantasy es una aplicación web que permite crear y gestionar ligas fantasy personalizadas a partir de torneos de fútbol no profesionales, como competiciones locales, amateur o solidarias. El proyecto surge como una alternativa flexible a las plataformas fantasy tradicionales, habitualmente limitadas a ligas oficiales, permitiendo adaptar el sistema fantasy a contextos más cercanos y personalizados.

## ⚙️ Tecnologías utilizadas

El proyecto está construido bajo una arquitectura moderna, garantizando escalabilidad, persistencia de datos y adaptabilidad a dispositivos móviles:

- **Backend:** Laravel (PHP 8.2+)
- **Frontend:** Blade, Alpine.js, Tailwind CSS, Bootstrap y JavaScript
- **Base de datos:** Single-Database con Eloquent ORM
- **Autorización:** RBAC multi-equipo con Spatie Laravel Permission
- **Despliegue y Contenedores:** Docker y Docker Compose
- **Aplicación Web Progresiva (PWA):** Service Workers y Web App Manifest para instalación nativa en dispositivos.

## 🖥️ Interfaz y Experiencia de Usuario (UX/UI)

El diseño se ha centrado en ofrecer una experiencia fluida y completamente *responsive*, permitiendo a los usuarios gestionar sus equipos tanto desde el ordenador como desde el teléfono móvil.

### Panel principal de Liguilla

Centraliza gran parte de la actividad del usuario: alineación, plantilla, clasificación y resultados mediante un sistema de navegación rápido por pestañas.

| Versión Escritorio | Versión Móvil |
| :---: | :---: |
| <img src="./docs/img/UserLiguillaPC.png" width="700"> | <img src="./docs/img/UserLiguillaMobile.png" width="250"> |

### Perfil de Usuario y Autenticación

Gestión completa de la cuenta, seguridad de contraseñas y opciones de persistencia de sesión.

| Registro y Autenticación | Gestión de Perfil |
| :---: | :---: |
| <img src="./docs/img/SignupPC.png" width="500"> | <img src="./docs/img/UserProfilePC.png" width="500"> |

### Panel de Administración

Cuadro de mando exclusivo para la gestión de torneos, equipos, jugadores, liguillas y usuarios.

| Dashboard de Administración |
| :---: |
| <img src="./docs/img/AdminHomePC.jpg" width="900"> |

## ⚽ Funcionalidad principal

La aplicación distingue dos roles principales de interacción:

### Usuario (Jugador)

- **Creación y participación:** Capacidad para unirse a ligas mediante códigos de invitación o crear liguillas privadas propias.
- **Gestión táctica:** Selección de jugadores, gestión de plantilla y configuración de la alineación titular.
- **Competición:** Las alineaciones se bloquean al inicio de cada jornada, compitiendo mediante un sistema de puntuación automatizado.
- **Instalación PWA:** Posibilidad de instalar la aplicación en iOS/Android directamente desde el navegador.

### Administrador (Gestor)

- **Mantenimiento de datos:** Creación y gestión de torneos, equipos y jugadores reales de la competición amateur.
- **Planificación:** Definición del calendario de jornadas y sus fechas límite de cierre.
- **Control global:** Supervisión del estado general de la competición y el sistema de puntuación.

## 🔮 Líneas de trabajo futuro

El proyecto establece una base sólida para el desarrollo continuo de nuevas funcionalidades:

- Despliegue en servidor de producción definitivo.
- Integración de un mercado de fichajes de jugadores dinámico.
- Sistema definitivo de cálculo de puntuaciones en tiempo real.
- Historial histórico de jornadas y estadísticas avanzadas para los participantes.
- Notificaciones automáticas (Push/Email) a usuarios sobre el estado de sus liguillas.
- Animaciones de transición y carga entre pantallas para mejorar la UX.

## 📚 Contexto académico y Autor

Este proyecto nace como idea de digitalizar un torneo solidario y aportar mayor participación involucrando al público con un juego tipo fantasy. A partir de esa iniciativa, se decidió llevar la idea a la universidad y convertirla en un Trabajo Fin de Máster, construyendo una plataforma real y completa desde cero.

**Autor:** Urki Aristu Viela  
**Contexto:** Trabajo Fin de Máster – Máster en Ingeniería Informática / UPNA

## 📄 Licencia y Derechos de Autor

Copyright © 2026 Urki Aristu Viela. Todos los derechos reservados.

Este repositorio contiene código protegido por las leyes de propiedad intelectual. Se permite su visualización pública exclusivamente con fines de demostración técnica y portafolio. Queda estrictamente prohibida la copia, reproducción, modificación, distribución o explotación comercial de todo o parte de este software sin la autorización expresa y por escrito del autor.
