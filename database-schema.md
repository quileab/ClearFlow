# Esquema de Base de Datos

## Tabla: `categories`
Define la naturaleza de los movimientos.
- `id`: primary key
- `name`: string (ej: Ventas, Alquiler, Impuestos)
- `type`: enum ('income', 'expense')
- `classification`: enum ('fixed', 'variable', 'none') -> 'none' se usa para ingresos.

## Tabla: `movements`
Registro individual de transacciones.
- `id`: primary key
- `category_id`: foreign key references `categories`
- `amount`: decimal(15,2)
- `method`: enum ('cash', 'bank')
- `date`: date
- `description`: text (nullable)

## Tabla: `daily_balances`
Para auditoría y rapidez de reportes.
- `date`: date (unique)
- `opening_balance_cash`: decimal(15,2)
- `opening_balance_bank`: decimal(15,2)
- `closing_balance_cash`: decimal(15,2)
- `closing_balance_bank`: decimal(15,2)