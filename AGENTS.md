# Prompt para Codex (Proyecto DAW) – Gestión de Cultivos y Variedades

Eres un asistente de programación. Trabajas en un proyecto DAW con **PHP + MySQL (mysqli)** y **Apache XAMPP**.

## Objetivo

Implementar la funcionalidad **“Gestión de Cultivos y Variedades”** e integrarla en el menú principal.

Se requiere una **pantalla de consulta** con filtros (estilo `consulta_parcela_sector.php`) y, desde ahí, poder realizar **CRUD** (ver/editar/eliminar) de:

- **Especies** (`especie`)
- **Variedades** (`varietat`)

Debe mantener **la misma estética/layout** que las pantallas actuales de Parcelas/Sectores.

## Reglas importantes (no saltártelas)

1. **NO inventes** nombres de archivos, tablas, columnas ni rutas. Si falta información imprescindible, **pregunta antes**.
2. Los **textos visibles** (títulos, botones, avisos) y los **comentarios del código** deben estar en **catalán**.
3. La carpeta pública es **`/HTML`** y el backend es **`/PHP`**.
4.
5. Reglas de borrado:
   - **Especie**: NO se puede eliminar si tiene variedades asociadas (`varietat.id_especie`). Mostrar aviso claro.
   - **Variedad**: NO se puede eliminar si tiene dependencias en `plantacio` o `sector_varietat`. Mostrar aviso claro.
6. La tabla `varietat` ya tiene el campo **`foto_url`** (`VARCHAR(255) NULL`).
   - Incluirlo en los formularios de crear/editar.
   - Mostrar miniatura en la consulta si existe (con `onerror` para ocultar si falla).
7. La consulta debe permitir filtrar por **especie** y por **variedad**.
   - El select de variedad debe ser **dependiente** de la especie vía **AJAX**.
8. Seguridad mínima:
   - Consultas con parámetros: **prepared statements**.
   - Validar IDs como enteros.
   - Evitar SQL injection.
9. Tras acciones (crear/editar/eliminar), redirigir a la consulta con `msg` o `err` por querystring.
10. **No introduzcas frameworks externos**. Solo PHP + mysqli + HTML/JS.

## Esquema BBDD (no inventar otros)

- `especie(id_especie PK, nom_cientific, nom_comu)`
- `varietat(id_varietat PK, id_especie FK, nom_cientific, nom_comu, caracteristiques_agronomiques, cicle_vegetatiu, requisits_pollinitzacio, productivitat_mitjana, qualitats_organoleptiques, foto_url)`
- `plantacio(id_plantacio PK, id_varietat FK, ...)`
- `sector_varietat(id_sector, id_varietat, id_data) PK compuesta, FK a varietat`

> IMPORTANTE: **No modificar la estructura de la BBDD**. `foto_url` ya existe.

## Archivos necesarios (crear o adaptar)

### 0) Conexión a BBDD

- **Reutiliza** el archivo de conexión existente si ya hay uno en el proyecto.
- Si NO existe ningún patrón reutilizable, crea `PHP/db.php` con una función `db_connect()` (mysqli, utf8mb4).

### 1) Pantalla principal de consulta

Crear: `PHP/consulta_cultius_varietats.php`

- Estética/layout: **igual** que `PHP/consulta_parcela_sector.php`.
- Filtros:
  - Select `id_especie` (todas/una)
  - Select `id_varietat` (todas/una), dependiente de especie
- Tabla listado:
  - Especie (nombre común + científico)
  - Variedad (nombre común + científico)
  - `productivitat_mitjana`
  - Foto (miniatura si `foto_url`)
  - Acciones (especie y variedad): **Ver / Editar / Eliminar**
- Botones:
  - “+ Nueva especie” → `especie_nova.php`
  - “+ Nueva variedad” → `varietat_nova.php`

### 2) AJAX de variedades por especie

Crear: `PHP/ajax_varietats_by_especie.php`

- Input: `GET id_especie`
- Output: JSON de objetos `{id_varietat, nom_comu, nom_cientific}`

### 3) CRUD Especies

Crear o adaptar:

- `PHP/especie_nova.php`
- `PHP/especie_editar.php`
- `PHP/especie_detall.php`
- `PHP/especie_eliminar.php`

Regla de eliminar:

- Antes de hacer `DELETE`, comprobar:
  - `SELECT COUNT(*) FROM varietat WHERE id_especie = ?`
  - Si > 0 → NO eliminar y mostrar aviso claro.

### 4) CRUD Variedades

Crear o adaptar:

- `PHP/varietat_nova.php`
- `PHP/varietat_editar.php`
- `PHP/varietat_detall.php`
- `PHP/varietat_eliminar.php`

Formularios:

- `id_especie` debe ser un **select** (NO pedir ID a mano).
- Campos:
  - `nom_cientific`, `nom_comu`
  - `caracteristiques_agronomiques`, `cicle_vegetatiu`, `requisits_pollinitzacio`
  - `productivitat_mitjana`, `qualitats_organoleptiques`
  - `foto_url`

Regla de eliminar:

- Antes de hacer `DELETE`, comprobar:
  - `SELECT COUNT(*) FROM plantacio WHERE id_varietat = ?`
  - `SELECT COUNT(*) FROM sector_varietat WHERE id_varietat = ?`
  - Si alguno > 0 → NO eliminar y mostrar aviso claro.

## Integración en el menú

- En el menú, añadir o actualizar la opción:
  - “Cultivos y variedades” → abre `../PHP/consulta_cultius_varietats.php`

## Estética y coherencia

- Para todas las páginas nuevas, reutiliza la misma estructura de HTML, CSS y componentes visuales que `consulta_parcela_sector.php`.
- No toques el estilo global, solo reutiliza.

## Entregable

1. Proporciona el **código completo** de cada archivo creado o modificado.
2. Indica los **cambios exactos** que haces en el menú de `HTML/index.php`.
3. Si detectas que ya existen archivos equivalentes (conexión BBDD, helpers, plantillas), **adáptate** al patrón existente y evita duplicados.

## Archivos del proyecto

El proyecto está en el repositorio abierto

