# Lógica de Negocio y Funcionalidades

## 1. Gestión de Saldos Diarios
- El **Saldo Inicial** de un día `D` debe ser igual al **Saldo Final** del día `D-1`.
- Si no existe registro del día anterior, el saldo inicial es 0 o se solicita carga manual por única vez.
- El Saldo Final se calcula como: `Saldo Inicial + Ingresos - Egresos` (separado por método Cash/Bank).

## 2. Validación de Carga
- Al cargar un movimiento, el sistema debe filtrar las categorías por `type`.
- Si el usuario elige "Ingreso", solo se muestran categorías con `type = income`.
- Si el usuario elige "Egreso", se muestran categorías con `type = expense`.

## 3. Reportes Requeridos
- **Vista Mensual**: Tabla con desglose diario y totales acumulados.
- **Vista Anual**: Matriz comparativa con los 12 meses, mostrando:
    - Total Ingresos.
    - Total Gastos Fijos.
    - Total Gastos Variables.
    - Variación porcentual entre meses.