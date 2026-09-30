# Production Checklist — Stripe & Bizum (Laravel SaaS)

Antes de activar el entorno de producción (Live Mode), completa y verifica minuciosamente cada uno de los siguientes pasos. PagoKit ha configurado el entorno seguro para sandbox/desarrollo; el paso a producción requiere verificación manual de seguridad y cumplimiento normativo.

## 1. Sustituir claves de prueba por claves de producción

- [ ] En el **Stripe Dashboard**, desactivar el interruptor *Modo de prueba* para ingresar a *Modo en vivo*.
- [ ] Configurar `STRIPE_KEY` con el valor `pk_live_...` en el almacén de secretos del servidor de producción.
- [ ] Configurar `STRIPE_SECRET` con el valor `sk_live_...` (NUNCA incluirlo en el repositorio ni en `.env.example`).
- [ ] Configurar `STRIPE_WEBHOOK_SECRET` con el secreto `whsec_...` generado específicamente para el webhook de producción.

## 2. Configurar el endpoint de Webhooks en el Dashboard en Vivo

- [ ] Añadir un nuevo endpoint con la URL pública HTTPS de tu aplicación: `https://<tu-dominio-produccion>/stripe/webhook`.
- [ ] Seleccionar los eventos requeridos:
  - `checkout.session.completed`
  - `customer.subscription.created`
  - `customer.subscription.updated`
  - `customer.subscription.deleted`
  - `invoice.payment_succeeded`
  - `invoice.payment_failed`
- [ ] Copiar la *Clave para firmar* (`whsec_...`) y asignarla a `STRIPE_WEBHOOK_SECRET` en producción.

## 3. Activar Métodos de Pago (Tarjetas + Bizum)

- [ ] En **Stripe Dashboard -> Configuración -> Métodos de pago**, asegurar la activación de **Bizum** y **Tarjetas de crédito/débito**.
- [ ] Confirmar que la cuenta de Stripe está constituida en España (ES) o jurisdicción soportada para cobros directos por Bizum.
- [ ] Crear los productos y precios recurrentes (mes a mes en EUR):
  - Plan Básico: `29.00 EUR/mes` -> asignar ID de precio a `STRIPE_PRICE_BASICO`
  - Plan Pro: `79.00 EUR/mes` -> asignar ID de precio a `STRIPE_PRICE_PRO`
  - Plan Enterprise: `199.00 EUR/mes` -> asignar ID de precio a `STRIPE_PRICE_ENTERPRISE`

## 4. Cumplimiento SCA / PSD2 (3D Secure)

- [ ] La autenticación reforzada de clientes (SCA) conforme a la directiva europea PSD2 está garantizada de forma nativa mediante la redirección segura a Stripe Checkout (PCI DSS SAQ A).
- [ ] Probar el desafío 3DS tanto en entorno de pruebas como con una tarjeta real de bajo importe.

## 5. Facturación y Cumplimiento RGPD / GDPR

- [ ] Activar y configurar el **Stripe Customer Portal** en el Dashboard para permitir a los suscriptores descargar facturas, modificar métodos de pago y gestionar cancelaciones.
- [ ] Configurar los datos fiscales de la empresa (NIF/CIF, razón social y régimen fiscal) en la sección de facturación de Stripe.
- [ ] Principio de minimización de datos (Regla 11): La aplicación únicamente transfiere a Stripe los campos estrictamente necesarios (`email`, `name`, `user_id`, `plan`, `org_name`), sin retener datos financieros sensibles en servidores propios.

## 6. Pruebas finales en Producción

- [ ] Ejecutar una transacción real de suscripción utilizando una tarjeta real o Bizum.
- [ ] Comprobar en el panel de eventos de Stripe que el webhook recibió respuesta `200 OK`.
- [ ] Verificar en la base de datos de producción que el Tenant fue aprovisionado y el rol `Admin Local` fue asignado correctamente con Spatie Teams.
- [ ] Emitir un reembolso o cancelación de prueba desde el Dashboard para verificar la sincronización de estado.

## 7. Monitorización y Alertas

- [ ] Habilitar notificaciones por correo en el Stripe Dashboard ante fallos de cobro recurrentes (`invoice.payment_failed`) o errores de entrega de webhooks.
- [ ] Supervisar los logs de Laravel (`storage/logs/laravel.log`) durante las primeras 48 horas tras el despliegue a producción.
