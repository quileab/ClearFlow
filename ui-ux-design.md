# Interfaz de Usuario (MaryUI)

## Layout Principal
- Sidebar de navegación con acceso a: Dashboard, Movimientos, Categorías y Reportes.
- Tema claro/oscuro soportado por DaisyUI.

## Componentes Clave
- **Dashboard**: Uso de `<x-stat />` para mostrar saldo actual en Caja y Banco.
- **Carga de Movimientos**: Usar `<x-drawer />` para formularios rápidos de creación.
- **Tablas**: Usar `<x-table />` con cabeceras ordenables para los movimientos.
- **Filtros**: Implementar selectores de fecha y categoría usando `<x-select />` y `<x-datepicker />`.

## Feedback Visual
- Los montos de ingresos deben mostrarse en verde (`text-success`).
- Los montos de egresos deben mostrarse en rojo (`text-error`).