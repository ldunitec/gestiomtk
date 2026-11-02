# Gym

Módulo independiente en `Modules/Gym` para socios, planes, membresías, pagos, validación de ingreso, entradas/salidas y asistencia.

## Instalación en el servidor IIS

Después de revisar e integrar este cambio en main, haga un respaldo de la base de datos y ejecute en PowerShell desde el proyecto. Complete la migración antes de habilitar el uso del menú:

```powershell
Set-Location 'C:\Web\gestionmtk'
git pull --ff-only origin main
php artisan optimize:clear
php artisan migrate --path=Modules/Gym/Database/Migrations --force
php artisan db:seed --class='Modules\Gym\Database\Seeders\GymDatabaseSeeder' --force
php artisan permission:cache-reset
php artisan view:clear
```

Cada comando debe terminar correctamente antes del siguiente. No ejecute el seeder general de roles ni migraciones ajenas para instalar Gym. Las tablas nuevas tienen prefijo `gym_`. La migración de Gym se ejecuta una sola vez; Laravel registra su aplicación. El seeder es aditivo e idempotente y otorga los permisos al rol existente `ADMINISTRADOR` sin quitar sus permisos anteriores. Si ese rol no existe, asigne los permisos de Gym mediante su administrador de roles.

Entre con su cuenta administradora, cierre y abra sesión si fuera necesario y abra **Gym** en el menú. URL de ejemplo: `http://10.10.30.60:8081/gestionmtk/gym`. Para otro equipo use la IP o dominio que corresponda a su servidor.

## Operación

1. Cree los planes (precio en lempiras, duración en días calendario y límite opcional de visitas).
2. Registre al socio; se generará un código único y permanente.
3. Abra su ficha y cree la membresía con fecha de inicio y pago inicial. El fin es inicio + días − 1, ambos días inclusivos.
4. Registre abonos hasta completar el precio. El historial conserva monto, referencia, operador y fecha. Solo una membresía completamente pagada y vigente permite entrar; los planes gratuitos permiten entrar sin pago.
5. En **Control de acceso**, escriba o lea el código y pulse Entrada o Salida. Un lector que actúe como teclado puede rellenar este campo. Solo se registra asistencia cuando se autoriza una entrada. Cada entrada autorizada consume una visita.
6. Consulte **Reportes de asistencia** por fechas y código; exporte a CSV para Excel. Los intentos rechazados se muestran por separado.

Las membresías del mismo socio no pueden superponerse. La renovación propone el día posterior al vencimiento de su última membresía no anulada. Cambiar o desactivar un plan no altera las condiciones de las membresías ya vendidas. No se admiten nuevas ventas de un plan inactivo.

Suspender/inactivar un socio, anular una membresía, vencer o adeudar saldo bloquea las próximas entradas. La salida de una asistencia abierta sigue permitida. Anular conserva el historial de pagos: no registra un reembolso. No hay eliminación de socios, planes o historial desde el módulo.

Una entrada abierta impide otra entrada el mismo día. Si el socio vuelve otro día y puede ingresar, la asistencia anterior queda **sin salida**, sin inventar una hora de salida. La asistencia se atribuye a la fecha de entrada. Las fechas de negocio y reportes usan Honduras (`America/Tegucigalpa`); los movimientos y pagos se guardan en UTC.

## Permisos

| Permiso | Función |
| --- | --- |
| `gym.ver` | Resumen y últimos movimientos |
| `gym.administrar` | Socios, planes, membresías, abonos y anulaciones |
| `gym.accesos` | Recepción, validación e ingresos/salidas por API |
| `gym.reportes` | Asistencia, rechazos y descarga de CSV |

Cada pantalla y escritura verifica su permiso en el servidor. Asigne a recepción solo los permisos necesarios.

## API para un lector o controlador

Requiere HTTPS antes de usar tokens fuera de un entorno de pruebas. Use un token Sanctum de un usuario con permiso `gym.accesos` y capacidad `gym:acceso`. No coloque el token en una página pública ni en código del navegador. No se crean credenciales automáticamente.

- `POST /api/gym/validar`: cuerpo JSON `{"codigo":"GYM-…"}`. Consulta elegibilidad de la membresía (estado, fechas, saldo y visitas); no comprueba ni registra un movimiento de puerta.
- `POST /api/gym/accesos`: cuerpo JSON `{"codigo":"GYM-…","movimiento":"entrada","solicitud_id":"UUID"}`. Use `salida` para cerrar la asistencia. Este endpoint es el resultado definitivo para registrar el ingreso, incluida la prevención de entradas abiertas.

En IIS con la aplicación actual, el prefijo completo es `/gestionmtk/api/gym/...`. Envíe `Authorization: Bearer TOKEN`, `Accept: application/json` y `Content-Type: application/json`.

La respuesta de registro contiene `permitido`, `motivo`, `id` y `solicitud_id`. Un rechazo de negocio devuelve HTTP 200 con `permitido:false`; un fallo de autenticación/permisos devuelve 401/403 y datos inválidos, 422. Un controlador debe revisar **permitido**, no solo el estado HTTP, y rechazar el acceso si no obtiene respuesta válida.

Cada movimiento requiere un UUID nuevo. Reintentar el mismo UUID con el mismo código, movimiento y operador devuelve el resultado original sin otra asistencia. No reutilice un UUID para una nueva entrada/salida; reutilizarlo con otros datos se rechaza. Una consulta previa de validación no garantiza que un movimiento posterior sea permitido.

La API registra la autorización del sistema, no confirma físicamente que una persona cruzó una puerta. Para torniquete, cerradura, QR o biométrico hace falta definir equipo, protocolo y señal de paso. Este cambio no abre puertas ni configura dispositivos.

## Verificación

`vendor/bin/phpunit tests/Feature/GymTest.php` prueba permisos, seeder aditivo, pantallas, creación, vigencia inclusiva en Honduras, saldos, límite de visitas, salidas, idempotencia, renovación, CSV y API. GitHub Actions ejecuta estas pruebas con PHP 8.3 y SQLite. La migración usa tipos y claves compatibles con MariaDB; la integración con el servidor IIS y hardware debe verificarse en ese entorno.
