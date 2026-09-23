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
| 2 | Badge de notificaciones no se oculta con `vistas=1` | `js/app.js`, `herramientas/menu.php` | ⏸️ **En espera** — se deja como está por decisión del propietario (2026-09-23) | Bajo | Comportamiento a confirmar |
| 3 | Cupones / Multicupón / Vales sin el rediseño de la app | `tarjetas.php`, `megacupones.php`, `js/app.js`, `css/custom.css` | ✅ **Parcialmente corregido** (esta sesión) — solo `obtener_vales`, sin carrusel | Medio (de lo acordado) | Producto / diseño |
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

## 2. [En espera] El badge de notificaciones no se oculta cuando `vistas=1`

> **Decisión (2026-09-23):** se deja el comportamiento actual del dashboard sin cambios por ahora. El badge sigue mostrando el número real de no leídos aunque se llame con `vistas=1`. No se ha tocado código.

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

## 3. [Parcialmente corregido] Cupones / Multicupón / "Mis vales" — rediseño no portado

**Dónde:**
- `obtener_multicupon` — dashboard: `js/app.js:1921-2120` (200 líneas) / app: `js/app.js:2105-2497` (393 líneas). Se usa desde `tarjetas.php`. **No tocada.**
- `obtener_vales` — dashboard: `js/app.js:2397-2640` (244 líneas antes del cambio) / app: `js/app.js:2946-3245` (300 líneas). Se usa desde `tarjetas.php` y `megacupones.php`. **Modificada.**

Estas dos funciones son, con diferencia, las que más han divergido. No se trata de un bug puntual sino de una reestructuración de UI completa que la app ha ido incorporando y el dashboard nunca recibió:

- **Imagen propia por cupón** (`value.Img`): en la app cada cupón puede mostrar una imagen en vez del texto genérico del tipo de descuento; en el dashboard todos los cupones se seguían mostrando solo con texto.
- **Carrusel (Swiper)** para navegar entre multicupones, con spinner de carga (`mostrarSpinner()` / `beforeSend`) mientras llega la respuesta del servidor; en el dashboard no hay ni spinner ni carrusel, se pinta todo de golpe.
- **Badge "Descuento acumulable"** (`temp_lang['DescuentoACUMULA']`) para cupones marcados como `diferido == 1`; el dashboard no distinguía visualmente este tipo de cupón.
- **ChequeAhorro con maquetación propia** dentro del listado de "mis vales" (antes se pintaba igual que cualquier otro cupón, ahora tiene su propia cabecera, icono y disposición).

### Decisión de alcance (2026-09-23)

Tras revisar el coste de cada pieza, se decidió portar **solo lo funcional, sin el carrusel**:

| Pieza | ¿Se porta? | Motivo |
|---|---|---|
| Imagen por cupón (`value.Img`) | ✅ Sí | Aditivo, degrada bien si la API no manda imagen |
| Badge "Descuento acumulable" | ✅ Sí | Aditivo, i18n y icono ya existían en el dashboard |
| Tarjeta propia del ChequeAhorro en "mis vales" | ✅ Sí | Aditivo, no requiere marcado PHP nuevo |
| Carrusel Swiper (`obtener_multicupon`) | ❌ No | El dashboard no tiene el CSS de Swiper ni el marcado del carrusel; tocar la función sin eso dejaría código a medio portar. Esfuerzo alto, se deja para otra sesión si se decide abordarlo. |
| Agrupación de cupones por concepto (`agruparCuponesPorConcepto`) | ❌ No | Función completamente nueva en la app, no solicitada, no aporta nada sin el rediseño visual completo alrededor. |

**Investigación previa a tocar código** (para no dar nada por hecho):
- Los contenedores donde se pinta el HTML (`.pagina-tarjeta .grup_cupones`, `.pagina-multi-cupon .otros-cupones-seccion-cupones`) **ya existían** en `tarjetas.php` — no hizo falta tocar PHP.
- Las claves de idioma `MiChequeAhorro`, `DescuentoACUMULA` y `cheque_canjeable`, y el icono `img/svg/cupon_euro.svg`, **ya existían** en el dashboard — no hizo falta añadir nada de eso.
- **Colisión detectada y evitada:** la app reutiliza la clase CSS `cheque_ahorro1` para la nueva tarjeta. Pero `tarjetas.php:15` ya usa esa misma clase para un widget distinto (`#mcheque_ahorro1`, el resumen de cheque-ahorro de la cabecera de la página). Copiar la clase tal cual habría alterado ese otro widget sin querer. Se usó un nombre nuevo, `cheque_ahorro_vale`, exclusivo de esta tarjeta.

### ⚠️ Corrección importante descubierta después de implementar (2026-09-23)

El primer intento de este punto modificó `js/app.js` (ver más abajo), pero **resultó ser código muerto**: ni `tarjetas.php` ni `megacupones.php` cargan `js/app.js`. Cada una tiene su propia copia de `obtener_multicupon()`/`obtener_vales()` escrita como `<script>` inline dentro del propio PHP, totalmente independiente. Encontrado tras revisar en el navegador que las imágenes no aparecían.

Arquitectura real descubierta (tres copias distintas de la misma lógica):
1. **`tarjetas.php:124`** — su propia `obtener_multicupon()`, y **`tarjetas.php:324`** — `obtener_vales1()`, que solo mantiene vivos los widgets `.cheque_ahorro`/`.cheque_ahorro1`; su parte de "otros vales" tiene el `.append()` comentado a propósito (deshabilitado para no duplicar con el punto 2).
2. **`megacupones.php:258`** — su **propia** `obtener_multicupon()`, que sobrescribe a la de `tarjetas.php` (esta página hace `require "tarjetas.php"` y luego redefine la función). Es la que pinta la rejilla "MULTICUPÓN" de la captura de pantalla. **Corregida.**
3. **`megacupones.php:188`** — su **propia** `obtener_vales()`, que sí pinta la lista de "otros vales" en `.wrapper-content-cupones` (bajo la rejilla de multicupón). **Corregida.**
4. **`js/app.js`** — la copia que edité primero, que no se ejecuta desde ninguna página del dashboard. Se deja el cambio hecho (es inofensivo, ya que no se ejecuta) por si en el futuro se decide que estas páginas pasen a usar el `app.js` compartido en vez de tener su propia copia — pero **el fix real está en `megacupones.php`**.

### Cambios aplicados — `megacupones.php` (las funciones que realmente se ejecutan)

**`obtener_multicupon()` (línea 258, rejilla "MULTICUPÓN"):** en el bucle de `value.promos`, si `value2.Img` viene informado se muestra `<img class="imagen_multicupon">`. Se mantiene la misma rejilla de columnas (`col-xs-4`/`col-xs-6`/`col-xs-2`) sin cambios.

**`obtener_vales()` (línea 188, lista de "otros vales" bajo la rejilla):** mismo tratamiento de imagen para `value.Img`, más el badge "Descuento acumulable" cuando `value.diferido==1` (esta función nunca renderiza el vale de ChequeAhorro — lo salta explícitamente en la línea 211 — así que no hacía falta portar la tarjeta especial de ChequeAhorro aquí).

**Corrección (misma sesión):** el primer intento sustituía el `<h1>` del tipo de descuento (el porcentaje, "20%", "15%"...) por la imagen — es decir, mostraba imagen **o** porcentaje. Al probarlo en el navegador se vio que en la app se muestran **ambos a la vez** (imagen arriba, porcentaje debajo). Se corrigió para que la imagen se añada *además* del `<h1>` del tipo, no en su lugar — igual que hace la app (con la única excepción de que si `tipo` ya trae una imagen incrustada en el propio HTML, no se duplica el `<h1>`).

### Cambios aplicados (primer intento, código muerto pero dejado tal cual) — `js/app.js`

**`js/app.js`, función `obtener_vales`** (se modificó tanto el bloque `success` como el `error` — el dashboard duplica la lógica de pintado en ambos para poder mostrar una versión en caché si falla la petición, así que se mantuvieron sincronizados):

1. Se añadió el guard `&& !encontrado_cheque_ahorro` a la condición `es_cheque_ahorro_principal==1`, igual que en la app — evita volver a procesar el bloque de ChequeAhorro si la API alguna vez devolviese más de una fila marcada como principal.
2. Para cupones normales: si `value.Img` viene informado, se muestra `<img class="imagen_multicupon">` en vez del texto del tipo; si no, se mantiene el comportamiento anterior exactamente igual.
3. Para cupones con `diferido==1`: se añade `<span class="bloque_cupon_acumula">` con el texto ya traducido `DescuentoACUMULA`.
4. Para el vale marcado `es_cheque_ahorro==1`: en vez de maquetarse como un cupón cualquiera, ahora se pinta con la clase `cheque_ahorro_vale`, icono `cupon_euro.svg`, título "Mi ChequeAhorro" y solo la primera línea del texto explicativo (antes del primer `<br>`).

**`css/custom.css`** (añadido al final del archivo): reglas para `.bloque_cupon_acumula`, `.imagen_multicupon` y `.cheque_ahorro_vale` (esta última adaptada de la regla `.cheque_ahorro1` de la app, renombrada para evitar la colisión explicada arriba).

**Nota menor:** las reglas de `.cheque_ahorro_vale` usan `font-family: "Nunito"` / `"NunitoMedium"`, que la app tiene cargadas como webfont pero el dashboard no. No se ha importado esa fuente (estaba fuera del alcance "solo funcional") — el texto usará la fuente por defecto del dashboard en su lugar, sin errores, solo con una tipografía ligeramente distinta a la de la app en esa tarjeta concreta.

**Lo que queda pendiente si en el futuro se decide ir a por la paridad completa:** el carrusel Swiper en `obtener_multicupon` (requiere `css/plugins/swiper/swiper.min.css`, que no existe en el dashboard, más marcado nuevo en `tarjetas.php`) y la función de agrupación de cupones.

**Cómo revisarlo en el dashboard:**
1. Abre `megacupones.php` (menú "Cupones") con una tarjeta de cliente que tenga multicupón y/o vales activos.
2. En la rejilla "MULTICUPÓN" de arriba: si algún producto de la API trae imagen, debe verse la imagen en vez del porcentaje/tipo de descuento en la casilla izquierda. Si no trae imagen, se ve exactamente igual que antes (no puedo confirmar desde aquí si la API ya envía imágenes en este entorno para este endpoint).
3. En la lista de "otros vales" de más abajo: mismo comportamiento de imagen, más la etiqueta amarilla "DESCUENTO ACUMULA" para los vales marcados como diferidos.
4. El widget de ChequeAhorro de la cabecera de la página (fuera de esta rejilla) no debería haber cambiado — esta función lo excluye explícitamente de su renderizado.
5. Revisa la consola del navegador (F12) para descartar errores JS al cargar `megacupones.php`.

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

1. ~~**Punto 1** (texto sin traducir)~~ — ✅ aplicado el 2026-09-23.
2. ~~**Punto 2** (badge de notificaciones)~~ — ⏸️ en espera por decisión del propietario (2026-09-23): se deja el comportamiento actual.
3. ~~**Punto 3** (cupones/vales)~~ — ✅ aplicada la parte funcional el 2026-09-23 (sin carrusel, por decisión del propietario). **Punto 5**: sigue pendiente de decisión.
4. **Punto 4**: en espera, solo se retoma si aparece una incidencia real con el QR.
