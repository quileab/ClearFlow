# ClearFlow

ClearFlow es una aplicación moderna de gestión financiera diseñada para proporcionar claridad y control sobre los flujos de fondos. Desarrollada con el stack TALL (Tailwind CSS, Alpine.js, Laravel y Livewire) y utilizando la biblioteca de componentes MaryUI, ofrece una interfaz reactiva y pulida para la toma de decisiones.

## Características Principales

### Dashboard Interactivo
- **Termómetro de Salud:** Comparativa diaria de ingresos vs. egresos para detectar picos de actividad.
- **Radar de Eficiencia:** Visualización de la estructura de costos (Gastos Fijos vs. Variables).
- **Indicador de Liquidez:** Evolución del saldo acumulado en Caja y Banco durante los últimos 30 días.
- **Evolución Anual:** Tendencia neta del negocio en los últimos 12 meses.
- **Filtro de Fechas:** Selector dinámico "Desde/Hasta" para analizar periodos específicos en tiempo real.

### Reportes Detallados
- **Vista Mensual:** Detalle diario de movimientos, ingresos, egresos y resultado neto.
- **Vista Anual:** Resumen por mes de ingresos, gastos fijos y variables.
- **Navegación Ágil:** Filtros por año y mes con interfaz optimizada.

### Gestión de Movimientos y Categorías
- Registro de ingresos y gastos con clasificación por método (Caja/Banco).
- Categorización flexible con tipos (Ingreso/Egreso) y clasificaciones (Fijo/Variable).

### Administración y Seguridad
- **Gestión de Usuarios:** Panel de administración para control de accesos (exclusivo para administradores).
- **Perfil de Usuario:** Espacio para que cada usuario gestione su información personal y contraseña.
- **Control de Roles:** Restricciones de seguridad basadas en roles (Admin/User).

## Stack Tecnológico

- **Framework:** Laravel 12
- **Frontend:** 
  - Livewire 4 & Volt
  - MaryUI (Componentes Blade)
  - Tailwind CSS 4
  - Alpine.js
- **Gráficos:** Chart.js
- **Base de Datos:** SQLite (desarrollo) / MySQL (producción)

## Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/quileab/ClearFlow.git
   cd ClearFlow
   ```

2. **Instalar dependencias:**
   ```bash
   composer install
   npm install
   ```

3. **Configuración de entorno:**
   - Copiar el archivo `.env.example` a `.env`:
     ```bash
     cp .env.example .env
     ```
   - Generar la clave de la aplicación:
     ```bash
     php artisan key:generate
     ```
   - Configurar los parámetros de base de datos en el archivo `.env`.

4. **Migraciones y Datos de Prueba:**
   ```bash
   php artisan migrate --seed
   ```
   *Nota: El seeder genera 3 años de datos simulados para una experiencia completa.*

5. **Compilar activos y ejecutar:**
   ```bash
   npm run dev
   # En otra terminal
   php artisan serve
   ```

## Licencia

Este proyecto está bajo la licencia **GNU GPL v3**. Consulta el archivo [LICENSE](LICENSE) para más detalles.
