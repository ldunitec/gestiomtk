# Actividades técnicas para gestiomtk

Integración del adjunto MEGATK_ActividadesTecnicas_Laravel13.zip adaptada a **Laravel 10, PHP 8.3 y nwidart/laravel-modules 10**. No actualiza Laravel ni Composer ni reemplaza la agenda de Producción.

## Alcance incluido

- Tablero con totales por estado y órdenes recientes.
- Listar, crear, consultar y editar órdenes (título, descripción, prioridad y fecha).
- Cancelar órdenes nuevas conservando registros, historial y auditoría. Una cancelación repetida no duplica el historial; las canceladas no se editan.
- Números OT con fecha y el ID global de la orden, sin contar filas para generar consecutivos.
- Sesión y permisos Spatie, navegación AdminLTE y validaciones con errores visibles.
- Trece tablas y sus modelos del adjunto para técnicos, clientes, ubicaciones, tipos de servicio, órdenes, asignaciones, tiempos, GPS, evidencias, materiales, aprobaciones, historial y auditoría. Usan el prefijo **at_** y solo referencian la tabla `users` existente.
- Fechas persistidas en UTC y presentadas en la zona configurada por la aplicación (America/Tegucigalpa en esta instalación).

El ZIP es una base inicial. **No incluye pantallas para catálogos/asignación, captura GPS/fotos/firmas ni transiciones operativas de estados**. Estas estructuras se conservan para desarrollarlas después; no se anuncia su funcionamiento como si estuvieran implementadas. Tampoco se importa información de otros módulos.

## Instalación en PowerShell

Después de integrar el PR, respalde su base y el proyecto. Desde `H:\xampp\htdocs\mtk` o `C:\Web\gestionmtk`, ejecute cada comando solo si el anterior terminó correctamente:

```powershell
git pull --ff-only origin main
composer dump-autoload
php artisan optimize:clear
php artisan module:list
php artisan migrate --path=Modules/ActividadesTecnicas/Database/Migrations --force
php artisan db:seed --class='Modules\ActividadesTecnicas\Database\Seeders\ActividadesTecnicasDatabaseSeeder' --force
php artisan permission:cache-reset
php artisan view:clear
```

No ejecute todas las migraciones pendientes ni el seeder general de roles. No copie encima del proyecto las carpetas del ZIP original: usan otra estructura. No requiere `composer update`, dependencias nuevas ni actualizar PHP o Laravel.

`modules_statuses.json` activa ActividadesTecnicas. El provider carga vistas, migraciones, web y API con sus middleware y respeta la caché de rutas. El autoload `Modules\\` existente resuelve sus clases sin cambiar el composer raíz. El seeder crea permisos de manera aditiva y los concede al rol existente **ADMINISTRADOR** sin quitarle otros.

## Acceso y permisos

Menú **Producción → Actividades técnicas**. Ruta relativa: `/admin/actividades-tecnicas`. En IIS con la aplicación bajo `/gestionmtk`: `/gestionmtk/admin/actividades-tecnicas`.

| Permiso | Alcance |
| --- | --- |
| actividades-tecnicas.ver | Tablero, lista, consulta e historial |
| actividades-tecnicas.gestionar | Crear y editar; requiere también ver |
| actividades-tecnicas.cancelar | Cancelar órdenes nuevas; requiere también ver |

El endpoint de diagnóstico `/api/v1/actividades-tecnicas/health` requiere Sanctum y `actividades-tecnicas.ver`; no admite captura móvil ni expone datos sin autenticación. No se crean tokens automáticamente.

## Comprobación

1. Ingrese como ADMINISTRADOR y abra el tablero y la lista.
2. Cree una orden con prioridad y fecha: compruebe su número OT, fecha local e historial «Orden creada».
3. Edite título/fecha y verifique los datos. Pruebe validaciones y permisos de solo lectura.
4. Cancele una orden nueva y compruebe que permanece consultable con su historial y ya no permite edición.
5. Compruebe que Agenda, Gym y MikroTik conservan sus accesos.

Para desinstalar en un entorno de prueba, haga rollback **solo del módulo y en orden inverso**; sus tablas contienen datos y no deben borrarse en producción. No use `migrate:fresh` para instalarlo.


## Fusión con Agenda de Producción

Después de actualizar el código, respalde la base y ejecute:

```powershell
php artisan migrate --path=Modules/ActividadesTecnicas/Database/Migrations/2026_11_07_000001_link_work_orders_to_agendas.php --force
php artisan actividades-tecnicas:integrar-agenda
php artisan optimize:clear
```

- Las órdenes nuevas crean una sola actividad en la agenda, con el mismo título y día. Sin fecha aparecen en «Sin agendar». El formulario permite elegir tipo y responsable de la agenda existente.
- En la agenda, abra una actividad y pulse **Convertir actividad en orden técnica**. Se reutiliza esa actividad y se conservan sus contactos, asignación, proyecto e IDs de Todoist. Repetir la conversión abre la misma orden. Los títulos de órdenes técnicas admiten hasta 180 caracteres.
- La agenda conserva sus responsables originales; esos identificadores no se convierten en usuarios locales ni en asignaciones técnicas ficticias.
- Mover de día o cambiar el título en la agenda actualiza la orden. La hora de la orden se conserva al mover el día; la agenda existente trabaja por días. Completar/reabrir desde agenda cambia el estado de la orden y registra historial. Las órdenes canceladas o aprobadas no se reabren desde la agenda.
- Cancelar una orden nueva retira su actividad de la agenda activa sin borrar historial. La descripción técnica completa permanece en la orden; el servicio/contactos de la actividad son sus propios datos y no se sobreescriben con la descripción al editar la orden.
- Crear/editar/cancelar una orden usa el mismo flujo de Todoist de la agenda después de confirmar la transacción local. Un fallo remoto muestra aviso y conserva los cambios. Convertir una actividad o ejecutar el comando no crea tareas remotas adicionales. Cerrar una orden cancelada usa el cierre existente de Todoist.
- El comando vincula las órdenes antiguas que aún no tienen agenda, sin duplicar las ya vinculadas. No intenta asociar actividades por títulos parecidos ni elimina actividades anteriores. Se puede ejecutar otra vez.
- Las actividades independientes siguen funcionando sin crear órdenes automáticamente. Para editar una actividad vinculada se exige también `actividades-tecnicas.gestionar`; consultar la orden exige `actividades-tecnicas.ver`.
- La migración añade una relación única `agenda_id` y completa únicamente las columnas opcionales de agenda ausentes en migraciones antiguas. Conserva los datos existentes. Para deshacer la relación use MariaDB; no retire columnas de compatibilidad de agenda que ya contienen datos.

El resto del alcance del adjunto (capturas GPS, evidencias y asignaciones operativas) conserva las limitaciones indicadas arriba.
