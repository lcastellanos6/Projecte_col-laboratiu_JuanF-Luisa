# SISTEMA CODEX - MODO PROFESIONAL

## CONTEXTO
Proyecto DAW con:
- Base de datos (`web.sql`)
- Codigo PHP/HTML
- Documentacion

---

## ARQUITECTURA

### 1. Agente Auditor
- Analiza:
  - SQL (PRIORIDAD)
  - Codigo
  - Documentacion
- Detecta:
  - Que existe
  - Que falta
  - Inconsistencias

---

### 2. Agente Implementador
- Ejecuta cambios
- Cambios pequenos
- No rompe estructura existente
- No asume nada

---

### 3. Agente Validador
- Verifica:
  - Coherencia con SQL
  - Coherencia con codigo
  - Impacto del cambio

---

## FLUJO OBLIGATORIO

1. Analisis
2. Plan de cambios
3. Implementacion
4. Validacion

---

## REGLA CRITICA

No puedes implementar nada sin haber hecho antes:

1. Analisis
2. Plan de cambios

Si falta informacion:
-> detenerse y pedir aclaracion

---

## ORDEN DE VERIFICACION

1. SQL (fuente de verdad)
2. Codigo
3. Documentacion

---

## PROHIBIDO ASUMIR

Clasificar siempre como:

- CONFIRMADO POR SQL
- CONFIRMADO POR CODIGO
- CONFIRMADO POR DOCUMENTACION
- INFERIDO
- NO ENCONTRADO
- DUDOSO

---

## GESTION DE INCERTIDUMBRE

- NO ENCONTRADO -> detener y pedir datos
- DUDOSO -> continuar con advertencia
- INFERIDO -> permitido, pero justificar

---

## REGLAS DE CAMBIO

- Cambios pequenos
- No refactor masivo
- No romper estructura
- No inventar nombres

---

## REGLA DE LOGICA UNICA

No se permite duplicar logica.

Toda logica critica debe:
- venir de una unica query
- o una unica funcion

---

## VALIDACION OBLIGATORIA

- Rompe algo existente?
- Es coherente con SQL?
- Afecta otras partes del sistema?

---

## EJECUCION DE TAREAS LOCALES

- En cada ejecucion, revisar este directorio y detectar archivos `Tarea*.md`.
- Si el usuario solicita implementar o auditar tareas, ejecutar las instrucciones definidas en esos `Tarea*.md`.
- Aplicar siempre el flujo obligatorio: analisis -> plan de cambios -> implementacion -> validacion.
