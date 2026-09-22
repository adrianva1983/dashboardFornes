# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

This is the "área clientes" (customer area) web dashboard for **masymas Fornes**, a supermarket loyalty program. It is a classic PHP 5/7-style procedural site (no framework, no Composer, no build step, no package.json) that runs on a WAMP/Apache+MySQL stack. There are no automated tests, linters, or build commands — changes are made directly to the PHP/JS/CSS files and verified by loading the pages in a browser.

The UI is built on the **INSPINIA** Bootstrap admin template (`css/`, `js/inspinia.js`, `js/plugins/*`). Client-side i18n (ES/EN/Valencià) is done via `tkey`/`tkeyholder` HTML attributes translated at runtime by `lang/lang.js` against the dictionaries in `lang/es.js`, `lang/en.js`, `lang/va.js` (`lang/old/` holds superseded versions).

`js/app.js` (~4.7k lines) is a large **shared** script that also powers a Cordova/PhoneGap mobile app build of this same product (SQLite plugin sync, barcode scanner, geolocation, `deviceready` handlers). Most of it is dead code in the browser-only dashboard context — don't assume every function in it is reachable from these PHP pages.

## Deployment layout (important for path resolution)

Every PHP page resolves shared includes via `$_SERVER['DOCUMENT_ROOT']`, assuming this repo is deployed at `<document_root>/dashboard/`. Several includes reach *outside* this repo into sibling directories that are **not** part of this repository:

- `$_SERVER['DOCUMENT_ROOT']."/Plantillas/..."` (e.g. `Plantillas/alerta_correo.php`, `Plantillas/Imagenes/logo.svg`)
- `$_SERVER['DOCUMENT_ROOT']."/includes/config.inc.php"` (referenced from `test.php`)
- `$_SERVER['DOCUMENT_ROOT']."/herramientas/libreria_front_office.php"` and `/Plantillas/idiomas/...` (root-level `herramientas`, distinct from this repo's `dashboard/herramientas/`)

When editing or tracing behavior that touches these paths, keep in mind the referenced files live in the parent "fornes" site, not in this repo.

## Request lifecycle (common page pattern)

Nearly every top-level `.php` page (`dashboard.php`, `compras.php`, `ahorro.php`, `perfil.php`, `megacupones.php`, `folletos.php`, `catalogos.php`, `comparte.php`, `notificaciones.php`, `habituales.php`, ...) follows the same skeleton:

1. `require .../Scripts-Gestion/conexion.php` — opens the `mysqli` connection (`$db`, global).
2. `require .../Scripts-Gestion/aut_verifica.inc.php` — authentication/session gate (see below).
3. Guard: redirect to `index.php` if `$_SESSION['cod_cliente']` isn't set.
4. HTML head + INSPINIA sidebar (`herramientas/menu.php`, passing a `$seccion` string to highlight the active item) + topbar (`herramientas/menu-top.php`).
5. Page body / business logic (often inline SQL + PHP-generated HTML).
6. `require .../Scripts-Gestion/cierre.php` — closes the `mysqli` connection.

`dashboard-tienda.php` is a variant page (store view) that uses `herramientas/menu-tiendas.php` instead of `menu.php`.

## Authentication (`Scripts-Gestion/aut_verifica.inc.php`)

Single file handles three login paths and doubles as the session gate for already-authenticated requests:

- **OAuth token** (`$_GET['token_oauth']`) — looks up `APP_login.token_oauth` (checked against `token_caducidad`), then resolves the associated card via `ClientesTarjetas`.
- **Card login** (`dni` + `cod_cliente` POSTed from `index.php`'s first form) — matches `ClientesTarjetas` by `id_card` + `co_nif`.
- **Username/password login** (`userName` + `password` POSTed from `index.php`'s second form) — matches `APP_login.nif`, password compared as `hash('sha256', $password) == $listado->pass`.

All three set `$_SESSION` (session name `AutenticadorPanel`): `cod_cliente`, `cod_cliente_digital`, `id_card`, `id_home`, `dni`, `select_idioma`, `token_oauth`. Logout is triggered by `?desconectar=si` or `?cerrar_sesion=1` (note: destroys a session named `Autenticador`, not `AutenticadorPanel` — pre-existing inconsistency in the code, not a rewrite target unless asked).

Key DB tables referenced across the app: `ClientesTarjetas` (customer/card records, `is_erased` soft-delete flag), `APP_login` (username/password + OAuth tokens), `APP_tarjetas_activas` (last-access tracking per card).

DB queries throughout the codebase are raw `mysqli_query()` with string-concatenated values (some `addslashes()`, most not). This is pre-existing legacy behavior — be aware of it when touching query code, but don't do drive-by SQL-injection hardening unless that's the actual task.

## External services

- `https://www.appfornes.es/servicios-web/*.php` — a separate backend API (mobile/web shared), called from `js/app.js` for messages, registration, SMS verification, sandbox mode, etc. Calls carry a shared `key_acceso` query param (visible client-side by nature of being a browser-side API key, not a server secret).
- `https://fornes.supermasymas.com/generar_top_productos.php` — fired (via `curl_init`, result unused) the first time a card is seen in `APP_tarjetas_activas`.
- `http://fornes.semillaproyectos.com/dashboard/` — hardcoded redirect target (`$redir`) used by the auth error paths in `aut_verifica.inc.php`.

## File naming conventions

- `*.php.bk` and `*_old.php` files (e.g. `dashboard.php.bk`, `megacupones.php.bk`, `index_old.php`, `folletos_old.php.bk`) are retained manual backups of earlier versions, not active code — don't edit or delete them as a side effect of unrelated changes, and don't treat them as the current implementation.
- `megacupones.php` directly `require`s `tarjetas.php` mid-page (not a standalone entry point in that flow).

## Credentials note

`Scripts-Gestion/conexion.php` contains a plaintext MySQL password. The GitHub repo (`adrianva1983/dashboardFornes`) this is pushed to is private by the owner's explicit choice — this is intentional, not an oversight to "fix" by scrubbing history or adding `.gitignore` unless asked.
