# TAREA - ALERTAS DE STOCK (PRODUCTE)

## OBJETIVO

Implementar un sistema de alertas de stock minimo para productos que:

- Detecte productos con stock bajo
- Muestre alertas en:
  - Vista de notificaciones (detalle)
  - Vista principal (contador o aviso)

---

## REGLAS ESPECIFICAS

- Solo aplicar a `producte`
- No aplicar a otras entidades (EPI, etc.)
- El stock siempre se calcula como:
  - `SUM(producte_lot.quantitat_disponible)`

---

## ANALISIS REQUERIDO

1. Verificar en SQL:
   - Tabla `producte`
   - Campo `stock_minim`
   - Tabla `producte_lot`
   - Relacion entre ambas
2. Confirmar:
   - Como se obtiene el stock actual
   - Si existen consultas similares

---

## PLAN DE CAMBIO

1. Crear query unica de alertas
2. Reutilizar esa query en:
   - Vista notificaciones
   - Vista principal
3. Implementar logica en PHP
4. Mostrar alerta visual

---

## IMPLEMENTACION

### 1. Query unica de alertas

```sql
SELECT
  p.id_producte,
  p.nom,
  p.stock_minim,
  COALESCE(SUM(pl.quantitat_disponible), 0) AS stock_actual
FROM producte p
LEFT JOIN producte_lot pl
  ON p.id_producte = pl.id_producte
GROUP BY p.id_producte, p.nom, p.stock_minim
HAVING COALESCE(SUM(pl.quantitat_disponible), 0) <= p.stock_minim;
```

### 2. Reutilizacion obligatoria

- Usar exactamente esta consulta (o una vista SQL equivalente) para ambas vistas.
- No duplicar logica de calculo en varias consultas distintas.

---

## VALIDACION OBLIGATORIA

- Verificar que un producto sin lotes devuelve `stock_actual = 0`.
- Verificar que solo aparecen productos con `stock_actual <= stock_minim`.
- Verificar que el contador/resumen de la vista principal coincide con el detalle.
- Verificar que no se afecta ninguna entidad distinta de `producte`.

---

## ENTREGA

Devuelve siempre:

1. Analisis
2. Cambios necesarios
3. Codigo exacto
4. Archivos afectados
