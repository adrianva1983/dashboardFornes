# Informe: funcionalidades desactualizadas en el dashboard respecto a la app

**Fecha:** 2026-09-23
**Comparado:** `C:\wamp64\www\fornes\dashboard` (este repo) vs `D:\000Apps\masymasfornes\www` (app Cordova/PhoneGap)
**Motivo:** ambos proyectos comparten un histórico común de `js/app.js`, pero solo la app ha recibido actualizaciones recientes. Este informe documenta dónde se ha detectado esa divergencia y qué se recomienda hacer con cada caso.

## Metodología

1. Se extrajeron las 120 funciones de primer nivel (`function nombre(...)`) que existen en **ambos** `app.js` (el del dashboard tiene 4.677 líneas, el de la app 6.075).
2. Se compararon cuerpo a cuerpo (ignorando espacios/formato): **100 de esas 120 difieren** de alguna forma.
3. Se descartaron los cambios que son solo de formato, logs de depuración (`console.log`), o funcionalidad exclusivamente nativa de Cordova (geolocalización, escáner de código de barras, SQLite local, notificaciones push) que no tiene sentido llevar a un dashboard de navegador.
4. Cada hallazgo que queda listado abajo se verificó contra el HTML/PHP real del dashboard antes de incluirlo, para descartar falsos positivos (por ejemplo, una función con el cuerpo "vacío" en el dashboard puede estar resuelta por otra función distinta en la misma página — eso se comprobó caso por caso).

## Resumen ejecutivo

| # | Hallazgo | Archivo(s) implicados | Estado | Esfuerzo | Tipo |
|---|----------|------------------------|--------|----------|------|
| 0 | Total de "Mi ChequeAhorro" mal calculado | `ahorro.php` | ✅ **Ya corregido** (esta sesión) | — | Bug |
| 1 | Texto "sin notificaciones" no traducido | `js/app.js`, `lang/*.js` | ✅ **Ya corregido** (esta sesión) | — | Bug menor / i18n |
| 2 | Badge de notificaciones no se oculta con `vistas=1` | `js/app.js`, `herramientas/menu.php` | Pendiente — **requiere decisión tuya** | Bajo | Comportamiento a confirmar |
| 3 | Cupones / Multicupón / Vales sin el rediseño de la app | `tarjetas.php`, `megacupones.php`, `js/app.js` | Pendiente | Alto | Producto / diseño |
| 4 | QR de la tarjeta con librería antigua | `tarjetas.php`, `js/app.js`, `js/qrcode.js` | Pendiente — sin evidencia de fallo | Medio | Técnico, sin urgencia |
| 5 | Falta "Eliminar cuenta" y "Cambiar email" | `perfil.php` | Pendiente | Alto | Producto / posible tema legal (RGPD) |

---

## 0. [Ya corregido] Total de "Mi ChequeAhorro" mal calculado

**Dónde:** `ahorro.php`, bloque `obtener_chequeahorro()`.

El dashboard mostraba como "Total" el valor `res.home_canjeable + res.home_acumulado` (solo el mes en curso), mientras que la app —y también el propio `dashboard.php` (pantalla de Inicio) y el `js/app.js` de este mismo repo— ya sumaban el mes actual **más** el mes anterior mediante una variable `total_acumulado` que se calculaba en el mismo bucle pero no se usaba para pintar el total.

- Antes: 12,00 € (solo septiembre)
- Después: 41,26 € (septiembre + agosto, igual que en la app)

**Acción tomada:** se reemplazó el bloque de cálculo por `total_acumulado`, que ya se calculaba correctamente en el mismo archivo. Sin cambios pendientes aquí.

---

## 1. [Ya corregido] Texto "sin notificaciones" no traducido

**Dónde:** `js/app.js`, función `obtener_mensajes` (dashboard: línea 2388).

Cuando no hay notificaciones, el dashboard imprime un texto fijo en español:

```js
// js/app.js:2388 (dashboard, actual)
$('#lista1').html('<div class="row lista_prod"><div class="col-xs-8 col-xs-offset-1 prod_data"><p class="prod_env">No tiene ningún mensaje nuevo.</p></div><div class="col-xs-2 prod_activo">&nbsp;</div></div>');
```

La app ya usa el sistema de traducción (`tkey`/`temp_lang`) para este mismo mensaje:

```js
// app.js:2770 (app, actual)
$('#lista1').html('<div class="row lista_prod lista_prod_mod"><div class="col-xs-12 prod_data"><p class="prod_env txt_negrita">' + temp_lang['sin_notificaciones'] + '</p><p class="txt_peq">' + temp_lang['sin_notificaciones_exp'] + '</p></div><div class="col-xs-2 prod_activo">&nbsp;</div></div>');
```

**Importante:** las claves `sin_notificaciones` y `sin_notificaciones_exp` **no existen todavía** en los diccionarios del dashboard (`lang/es.js`, `lang/en.js`, `lang/va.js`). Sí existen en los de la app:

| Clave | ES (app) | EN (app) | VA (app) |
|---|---|---|---|
| `sin_notificaciones` | "No hay notificaciones disponibles" | "No notifications available" | "No hi han notificacions disponibles" |
| `sin_notificaciones_exp` | "Cuando tengas una nueva notificación, te lo indicaremos en la campana de tu pantalla de inicio" | "When you have a new notification, we will indicate it in the bell on your home screen." | "Quan tingues una nova notificació, t'ho indicarem en la campana de la teua pantalla d'inici" |

**Acción tomada:**
1. Se añadieron `sin_notificaciones` y `sin_notificaciones_exp` a `lang/es.js`, `lang/en.js` y `lang/va.js` (junto a la clave `sin_conexion_perfil` existente), con los mismos textos que ya usa la app.
2. Se cambió la línea 2388 de `js/app.js` para usar `temp_lang['sin_notificaciones']` en vez del texto fijo. Se mantuvo la maquetación de un solo párrafo que ya tenía el dashboard (`col-xs-8 col-xs-offset-1`, clase `prod_env`) en vez de adoptar el segundo párrafo explicativo (`txt_peq` + `sin_notificaciones_exp`) de la app, porque esas clases CSS (`txt_negrita`, `txt_peq`, `lista_prod_mod`) no existen en las hojas de estilo del dashboard y se habrían visto sin estilo. La clave `sin_notificaciones_exp` queda ya traducida en los tres idiomas por si en el futuro se decide adoptar también esa segunda línea.

**Cómo revisarlo en el dashboard:**
1. Abre `notificaciones.php` con una tarjeta de cliente que no tenga mensajes pendientes (o marca todos los mensajes existentes como leídos/borrados desde esa misma pantalla).
2. Debe aparecer el texto **"No hay notificaciones disponibles"** en el bloque central, en vez del anterior "No tiene ningún mensaje nuevo.".
3. Cambia el idioma con el selector del menú (Castellano / Valencià / English) y repite la comprobación: el texto debe cambiar a "No hi han notificacions disponibles" (VA) o "No notifications available" (EN).
4. Como comprobación cruzada, abre la consola del navegador (F12) y verifica que no haya errores de JS al cargar `notificaciones.php` (un error típico si algo fuera mal sería `temp_lang is not defined` o `Cannot read properties of undefined`, lo que indicaría un problema de orden de carga de `lang.js`/`lang/es.js` — no debería ocurrir porque no se ha tocado ese orden).

Es un cambio pequeño, sin ambigüedad de comportamiento — puedo aplicarlo directamente si lo apruebas.

---

## 2. El badge de notificaciones no se oculta cuando `vistas=1`

**Dónde:** `js/app.js`, función `obtener_mensajes`; se invoca desde `herramientas/menu.php:154`.

`herramientas/menu.php` (que se incluye en todas las páginas del dashboard) llama así al cargar cualquier página:

```php
// herramientas/menu.php:154
obtener_mensajes(0, 1);
```

Es decir, siempre con `vistas=1`. En la versión actual de la app, cuando `vistas` vale `1`, el contador de "no leídos" se fuerza a 0 y el badge se oculta:

```js
// app.js:2745 (app, actual)
if (vistas == 1) $('.notif').html('0');
...
if (mensajes_no_leidos > 0 && vistas != 1) { $('.notif').show(); ... }
else $('.notif').hide();
```

En el dashboard, esa lógica no existe: con los mismos parámetros (`vistas=1`) el badge **sí** sigue mostrando el número real de no leídos.

**No sé si el cambio en la app fue intencional** (por ejemplo, para no duplicar el aviso cuando ya existe una notificación push nativa) o es un efecto colateral de otro cambio. Si lo porto tal cual al dashboard, el badge de "notificaciones sin leer" dejaría de mostrarse nunca en el dashboard (porque siempre se llama con `vistas=1`), lo cual podría no ser lo que quieres para la web.

**Necesito que me digas:** ¿el badge de notificaciones debe seguir mostrando el número de no leídos en el dashboard, o quieres que se comporte igual que en la app?

---

## 3. Cupones / Multicupón / "Mis vales" — rediseño no portado

**Dónde:**
- `obtener_multicupon` — dashboard: `js/app.js:1921-2120` (200 líneas) / app: `js/app.js:2105-2497` (393 líneas). Se usa desde `tarjetas.php`.
- `obtener_vales` — dashboard: `js/app.js:2397-2640` (244 líneas) / app: `js/app.js:2946-3245` (300 líneas). Se usa desde `tarjetas.php` y `megacupones.php`.

Estas dos funciones son, con diferencia, las que más han divergido. No se trata de un bug puntual sino de una reestructuración de UI completa que la app ha ido incorporando y el dashboard nunca recibió:

- **Imagen propia por cupón** (`value.Img`): en la app cada cupón puede mostrar una imagen en vez del texto genérico del tipo de descuento; en el dashboard todos los cupones se siguen mostrando solo con texto.
- **Carrusel (Swiper)** para navegar entre multicupones, con spinner de carga (`mostrarSpinner()` / `beforeSend`) mientras llega la respuesta del servidor; en el dashboard no hay ni spinner ni carrusel, se pinta todo de golpe.
- **Badge "Descuento acumulable"** (`temp_lang['DescuentoACUMULA']`) para cupones marcados como `diferido == 1`; el dashboard no distingue visualmente este tipo de cupón.
- **ChequeAhorro con maquetación propia** dentro del listado de "mis vales" (antes se pintaba igual que cualquier otro cupón, ahora tiene su propia cabecera, icono y disposición).

**Esto no es un bug** — es una decisión de diseño/producto de esfuerzo medio-alto (implica maquetar de nuevo dos funciones grandes y probablemente ajustar CSS). Lo dejo documentado para que decidas si merece la pena portarlo y con qué prioridad, pero no lo he tocado.

---

## 4. QR de la tarjeta — librería de generación distinta

**Dónde:** `pintar_codigo_qr` — dashboard: `js/app.js:1878-1890` / app: `js/app.js:2052-2079`. Se usa desde `tarjetas.php`.

```js
// dashboard, actual
function pintar_codigo_qr(data)
{
	var qrcode = new QRCode("barcode_codigo_tarjeta_qr",{
		width:256,
		height:256,
		colorDark: '#000000',
		colorLight: '#FFFFFF',
		useSVG: true,
		correctLevel : QRCode.CorrectLevel.H
	});
	qrcode.clear();
	qrcode.makeCode(data);
}
```

```js
// app, actual
function pintar_codigo_qr(data) {
	$('#barcode_codigo_tarjeta_qr').html('');
	var qr = qrcode(0, 'H');
	qr.addData(data, 'Numeric');
	qr.make();
	if (isDarkMode()) $('#barcode_codigo_tarjeta_qr').html(qr.createImgTag(10, 0));
	else if (!qr_ampliado) $('#barcode_codigo_tarjeta_qr').html(qr.createSvgTag(10, 0));
	else $('#barcode_codigo_tarjeta_qr').html(qr.createImgTag(10, 0));
	$('#barcode_codigo_tarjeta_qr svg').attr('width', '100%');
	$('#barcode_codigo_tarjeta_qr svg').attr('height', 'auto');
	$('#barcode_codigo_tarjeta_qr img').css('width', '100%');
}
```

La app cambió de la API antigua (`new QRCode(...)`) a una API distinta (`qrcode(0,'H')` + `addData`/`make`), y de paso añadió detección de modo oscuro (`isDarkMode()`, que no existe en el dashboard) y de "QR ampliado" (`qr_ampliado`).

**No hay evidencia de que el QR esté fallando en el dashboard** con la librería actual (`js/qrcode.js` / `js/qrcode.min.js`, que no se han tocado). Recomiendo **no tocar esto salvo que tengas constancia de algún problema concreto** de renderizado del QR en el dashboard — portar el cambio implicaría además revisar si el dashboard usa la misma librería subyacente que la app o si haría falta actualizarla.

---

## 5. Falta "Eliminar cuenta" y "Cambiar email" en el dashboard

**Dónde:** `perfil.php` — no hay ninguna referencia a estas funcionalidades en todo el archivo.

La app tiene implementados dos flujos completos que **no existen en absoluto** en el dashboard:

- **Eliminar cuenta:** `eliminar_cuenta()` (app.js:775) y `continuar_eliminar_cuenta()`.
- **Cambiar email:** `validar_cambio_mail()` (app.js:5905), `cambiar_email_confirmado()` (app.js:5990), más el envío de correos de confirmación (`mandar_email`, `mandar_email_reload`, `mandar_mail_alta`, `mandar_mail_mailjet`).

He revisado `perfil.php` completo y no hay ningún rastro de "eliminar cuenta", "baja", "RGPD" ni cambio de email — es un hueco funcional completo, no una versión antigua de algo que ya exista.

**Por qué podría importar más que los demás puntos:** si el derecho a eliminar la cuenta o cambiar el email de contacto es algo que estáis obligados a ofrecer también desde la web (RGPD / derecho al olvido), este sería el hallazgo con más peso de todo el informe, más allá de lo visual. Si es simplemente una funcionalidad que decidisteis que solo tuviera sentido en la app, entonces no hay nada que hacer.

**Necesito que me digas** si esto es una omisión a corregir o una decisión de producto ya tomada.

---

## Apéndice A — funciones exclusivas de la app (sin acción recomendada)

Del resto de funciones que existen en `app.js` de la app pero no en el del dashboard, casi todas caen en una de estas categorías, y no se recomienda portarlas salvo que pidas explícitamente alguna:

- **Gestión de cuenta/perfil (relacionado con el punto 5):** `cambiar_email_confirmado`, `mandar_email`, `mandar_email_reload`, `mandar_mail_alta`, `mandar_mail_mailjet`, `manejarCambioPrefijo`, `obtenerPrefijo`, `validarTelefono`, `validarTelefonoTodo`, `validarNombre`, `validarCadena`, `enviar_datos_personales_ajax`, `recuperar_tarjeta_interno`, `limpiar_tarjeta`, `cambiar_tarjeta_asociada`, `restaurar_boton_tarjeta`, `vincular_tarjeta_spinner`.
- **Sistema de notificaciones/modales in-app:** `abrirModal`, `cerrarModal`, `pintar_notificacion`, `pintar_notificacion_apertura` (popups de marketing al abrir la app), `confirmarCodigo`, `mostrar_mas_informacion`.
- **Cupones (relacionado con el punto 3):** `agruparCuponesPorConcepto`, `agruparCuponesPorConcepto1`, `desplegar_multicupon`, `ver_multicupon_principal`, `ver_multicupon_resaltar`.
- **Analítica / tracking de eventos:** `crear_eventos_track`, `registrar_evento`, `registrar_evento_nuevo`, `registrar_evento_scroll`.
- **Utilidades técnicas sin equivalente necesario en web:** `sleep`, `isDarkMode`, `valor_localstorage_valido`, `obtenerVersionAndroid`, `idioma_antiguo`, `obtener_clave_idioma`, `mostrar_spinner`, `ocultar_spinner`, `onOnline`, `onResume`.
- **Ubicación / tiendas (requieren geolocalización nativa):** `abrir_como_llegar`, `abrir_como_llegar_ios`, `refrescar_tiendas`.
- **Otros específicos de la app:** `abrir_email`, `abrir_tarjeta_asturias`, `descargar_ticket`, `informacion_asociar_tarjeta`, `visitar_TOL`.

## Apéndice B — funciones exclusivas del dashboard (informativo)

El dashboard tiene 10 funciones que no existen en la app, todas relacionadas con flujos propios de la web (login por usuario/contraseña, recuperación de contraseña) que la app no necesita porque usa otro sistema de autenticación:

`cerrar_modals_recuperar`, `cerrar_sesion`, `chequear1`, `ir_a_pagina_ant`, `ir_a_pagina_sgte`, `nobackbutton`, `recuperar_password`, `recuperar_password_mail`, `volver_perfil`, `ya_tengo_tarjeta1`.

No requieren ninguna acción — se documentan solo para que quede constancia de que la comparación fue en ambos sentidos.

---

## Próximos pasos sugeridos

1. **Punto 1** (texto sin traducir): puedo aplicarlo ya, es un cambio pequeño y sin ambigüedad.
2. **Punto 2** (badge de notificaciones): necesito tu decisión sobre el comportamiento deseado antes de tocar nada.
3. **Puntos 3 y 5**: decisiones de producto de mayor calado — dime cuáles quieres abordar y con qué prioridad para planificarlos como tareas separadas.
4. **Punto 4**: en espera, solo se retoma si aparece una incidencia real con el QR.
