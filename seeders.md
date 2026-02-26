# Definición de Seeders (Datos de Prueba)

## 1. CategorySeeder
Se deben crear las siguientes categorías base para cubrir las necesidades expresadas por el cliente:

| Nombre | Tipo (type) | Clasificación (classification) |
| :--- | :--- | :--- |
| Ventas de Productos | income | none |
| Prestación de Servicios | income | none |
| Cobro de Deudores | income | none |
| Alquiler Oficina | expense | fixed |
| Sueldos y Cargas Sociales | expense | fixed |
| Internet y Servicios | expense | fixed |
| Publicidad (Ads) | expense | variable |
| Insumos y Papelería | expense | variable |
| Mantenimiento y Reparaciones| expense | variable |
| Comisiones Bancarias | expense | variable |

## 2. MovementSeeder (Datos de Ejemplo)
Generar al menos 20 movimientos aleatorios distribuidos en los últimos 30 días para probar la reactividad de la interfaz:
- **Métodos**: Distribuir equitativamente entre 'cash' (Efectivo) y 'bank' (Banco).
- **Montos**: Valores realistas entre 5,000 y 150,000 para ingresos, y entre 1,000 y 80,000 para egresos.
- **Fechas**: Asegurar que haya múltiples movimientos en un mismo día para verificar el cálculo del saldo diario.

## 3. Lógica de Saldo Inicial
- Crear un registro en la tabla `daily_balances` para el primer día del mes actual.
- Establecer un `opening_balance_cash` de 50,000 y un `opening_balance_bank` de 200,000 para simular un estado de cuenta inicial.

## 4. UserSeeder
- Crear un usuario administrador con los siguientes datos:
    - Nombre: Admin
    - Email: admin@admin.com
    - Password: admin123
    - Role: admin