# MikroTik / Hotspot para visitantes

Módulo `Modules/Mikrotik` independiente de las pantallas antiguas de DHCP. Permite registrar routers, consultar recursos/interfaces y sesiones del Hotspot, crear fichas individuales o lotes de hasta 20, imprimir QR, desconectar y revocar visitantes.

## Requisitos y alcance

- Hotspot ya funcionando en el router, con su SSID y portal de inicio de sesión. No crea bridges, DHCP, NAT, VLAN, firewall ni cambia la salida a Internet.
- RouterOS **6.49.x (incluido 6.49.23) o 7.x** para esta implementación de vencimiento por fecha/hora. El módulo verifica versión y ejecución del script antes de habilitar fichas. No actualiza RouterOS.
- Reloj/NTP correcto tanto en el router como en Windows Server. Se rechaza la emisión si la hora reportada por el script difiere más de 90 segundos de la aplicación.
- API accesible desde el servidor Laravel. Use API-SSL con certificado válido o API sin TLS solo en LAN/VPN confiable. No exponga la API directamente a Internet. El módulo no abre puertos ni modifica servicios del router.
- Usuario API dedicado con acceso a las operaciones necesarias de lectura/escritura, Hotspot, scripts y scheduler. Las tareas creadas usan `read,write,policy,test`; el usuario de conexión necesita también acceso `api` y suficientes permisos para crear/ejecutar esos objetos. Revise las políticas con su administrador de RouterOS. No se crean cuentas del sistema del router.
- PHP con `sockets` y `openssl`, ya utilizados por la librería RouterOS existente. No hay paquetes nuevos.

No se ha ejecutado una prueba contra un router físico desde este entorno. La suite automatizada utiliza una API simulada; la sintaxis y los permisos efectivos de RouterOS se deben confirmar con la preparación y la prueba de aceptación descrita abajo.

## Instalación en IIS / PowerShell

Después de integrar el PR en main, respalde la base de datos y ejecute cada comando solo si el anterior terminó correctamente:

```powershell
Set-Location 'C:\Web\gestionmtk'
git pull --ff-only origin main
php artisan optimize:clear
composer dump-autoload
php artisan migrate --path=Modules/Mikrotik/Database/Migrations --force
php artisan db:seed --class='Modules\Mikrotik\Database\Seeders\MikrotikDatabaseSeeder' --force
php artisan permission:cache-reset
php artisan view:clear
```

Se usan únicamente tablas nuevas `mt_routers`, `mt_lotes`, `mt_fichas` y `mt_eventos`. No ejecute todas las migraciones pendientes ni el seeder general de roles para instalar este módulo. El seeder es aditivo e idempotente y concede los nuevos permisos al rol existente `ADMINISTRADOR`.

Abra **MikroTik / Hotspot** en el menú. URL del módulo en la instalación actual: `/gestionmtk/routers-mikrotik`. No se modifica `config/mikrotik.php` ni la conexión antigua del proyecto.

Las claves API y de las fichas se cifran con `APP_KEY`. Respalde y conserve esa clave junto con la base de datos; cambiarla sin migrar los cifrados impedirá recuperar las credenciales.

## Compatibilidad con RouterOS 6.49.23

La lectura de hora usa `/system clock` y su `gmt-offset`, sin `:timestamp` ni `:tonsec`. Se admiten la fecha clásica `nov/03/2026` y la fecha ISO `2026-11-03`. Aunque `clock print` muestra `-06:00`, `get gmt-offset` devuelve segundos. El script admite segundos con signo y segundos negativos representados como entero de 32 bits sin signo. Por ejemplo, el valor real observado `4294945696` se convierte a `-21600` segundos (UTC−06:00), restando `4294967296`. No es necesario cambiar la zona horaria ni el reloj por esta representación.

El cálculo considera años bisiestos y convierte a UTC antes de comparar, por lo que conserva los vencimientos originales de las fichas. Los desplazamientos numéricos fuera de ±14 horas se rechazan. La preparación exige que el router ejecute el script y devuelva una hora UTC dentro de 90 segundos de la aplicación; si falla, no se habilitan nuevas fichas.

Después de actualizar esta compatibilidad, abra **Configuración → Revisar preparación del Hotspot → Preparar y verificar Hotspot** para reinstalar el código de reloj. No hace falta una migración nueva ni actualizar el firmware. La API simulada cubre la aceptación de la versión y el flujo de emisión; el script debe verificarse en el equipo real antes de entregar fichas.

## Configurar su router

1. En **Configuración**, registre nombre, IP/DNS, puerto API, transporte, usuario y clave API.
2. Escriba el nombre exacto del servidor Hotspot existente, el SSID y la URL de su portal terminada en `/login` (sin parámetros). Use el portal real que puedan alcanzar los visitantes desde esa red, por ejemplo `https://wifi.sudominio.hn/login`.
3. Configure la velocidad por visitante: formato subida/bajada de RouterOS, por ejemplo `2M/5M`.
4. Pulse **Probar conexión**. Esta prueba solo lee recursos, identidad, interfaces y el servidor Hotspot.
5. Abra **Revisar preparación del Hotspot**. Allí se muestran los objetos y el código que se instalarán. Confirme con **Preparar y verificar Hotspot**.
6. La preparación crea o actualiza un perfil, un script y un scheduler con nombres exclusivos derivados del UUID del router, prefijados `gmtk-`. No altera perfiles de otros clientes. No edite manualmente esos objetos ni los comentarios de sus usuarios.

API-SSL verifica el certificado y el nombre del equipo. Para una CA privada ausente en PHP puede establecer en el `.env` del servidor:

```dotenv
MIKROTIK_VISITANTES_CA_FILE=C:\Certificados\ca-mikrotik.pem
```

El certificado debe ser válido para la IP o nombre utilizado. No se desactiva la verificación TLS. Dejar vacía la clave al editar conserva la existente. Con historial de fichas, el destino, transporte, Hotspot, SSID y portal quedan protegidos contra cambios accidentales; registre otra conexión si necesita un destino distinto. Cambiar usuario/clave o velocidad requiere preparar de nuevo.

## Vigencia de las fichas

| Selección | Vigencia desde creación |
| --- | --- |
| 1 día | 24 horas |
| 2 días | 48 horas |
| 1 semana | 168 horas |

El tiempo desconectado también cuenta. La vigencia no comienza en el primer ingreso y no se reinicia al reintentar una publicación.

Cada ficha tiene usuario y contraseña aleatorios. El perfil admite un dispositivo simultáneo y no añade MAC cookies. El usuario se restringe al servidor Hotspot elegido.

El comentario del usuario conserva el identificador del módulo y el instante de vencimiento en UTC. El perfil comprueba ese vencimiento al ingresar. El router ejecuta además una tarea cada 30 segundos que deshabilita usuarios vencidos y elimina sus sesiones/cookies. Una sesión ya conectada puede tardar hasta 30 segundos en cerrarse después del vencimiento cuando el scheduler está funcionando. Tras reiniciar el router, la validación de ingreso sigue vigente y la tarea vuelve a ejecutarse periódicamente.

No se usa `limit-uptime` como sustituto de la fecha de vencimiento: ese parámetro limita tiempo acumulado de conexión. Si el reloj del router es inválido, la protección bloquea las fichas del módulo. Corrija NTP, prepare/verifique el Hotspot y use **Verificar / reintentar publicación** para recuperar las fichas todavía vigentes. Las vencidas o revocadas no se reactivan.

La protección local sigue funcionando si Laravel queda desconectado, mientras el router conserve sus perfiles, scripts, scheduler y reloj correctos. No hay garantía de vencimiento si un administrador elimina/deshabilita esa protección.

## Emisión, impresión y QR

1. Abra **Fichas de visitantes**, seleccione el router preparado, vigencia y cantidad (1 a 20). La referencia del visitante es opcional.
2. Cree y publique el lote. El sistema habilita cada usuario después de verificar la protección y comprobar que corresponde a esa ficha. Una respuesta perdida se puede reintentar sin duplicar el usuario ni extender su vigencia.
3. Imprima una ficha o **Imprimir fichas vigentes** del lote. La impresión del lote omite las vencidas, revocadas o no confirmadas y muestra cuántas omitió. Puede guardar como PDF desde el navegador.
4. El visitante se conecta al SSID, escanea el QR e introduce la contraseña impresa.

El QR se genera localmente en SVG y abre el portal con el nombre de usuario precargado. **No conecta automáticamente al Wi-Fi ni autentica por sí solo**. La clave no se incluye en la URL del QR. Esto permite usar el portal existente con CHAP/HTTPS sin habilitar PAP ni modificar sus métodos de autenticación. Si su plantilla personalizada no recoge el parámetro `username`, el visitante puede escribir el usuario impreso.

No se utiliza un servicio externo de QR ni una página pública de Laravel que exponga las claves. La impresión requiere sesión y permiso, y se entrega con `Cache-Control: no-store`. Las fichas impresas contienen credenciales de acceso; entréguelas únicamente al visitante correspondiente.

## Sesiones y revocaciones

En el resumen, consulte el estado actual del router y abra **Sesiones Hotspot**. Puede ver usuario, IP, MAC, tiempo conectado y bytes de la sesión. Solo puede desconectar mediante este módulo a usuarios de sus propias fichas.

- **Desconectar** cierra la sesión y elimina sus cookies, conservando la vigencia de la ficha. El visitante puede volver a ingresar.
- **Revocar** guarda primero la solicitud en la base de datos y después intenta deshabilitar al usuario y cerrar sus sesiones.
- Si la API está fuera de línea, se muestra **Revocación pendiente**: el corte todavía no está confirmado y el visitante podría continuar hasta el vencimiento original. No se presenta un corte pendiente como confirmado.
- **Conciliar vencimientos y revocaciones** reintenta los bloqueos pendientes y actualiza su estado en la aplicación.

Para conciliar automáticamente, el módulo registra `mikrotik:conciliar` cada minuto en el scheduler de Laravel. Asegúrese de tener una tarea de Windows que ejecute `php artisan schedule:run` cada minuto desde `C:\Web\gestionmtk`; si ya existe, reutilícela. El vencimiento en el router no depende de esa tarea; las revocaciones pendientes y su confirmación sí necesitan un reintento manual o programado.

Prueba manual del conciliador:

```powershell
Set-Location 'C:\Web\gestionmtk'
php artisan mikrotik:conciliar
```

## Permisos

| Permiso | Función |
| --- | --- |
| `mikrotik.ver` | Resumen, recursos, interfaces e historial |
| `mikrotik.configurar` | Conexiones, prueba y preparación del router |
| `mikrotik.fichas` | Emitir, imprimir, verificar, revocar y conciliar |
| `mikrotik.sesiones` | Consultar y desconectar sesiones propias |

Los permisos se comprueban también en las rutas de escritura. Se guarda historial de operaciones con router, ficha, operador, fecha y resultado, sin contraseñas.

## Prueba de aceptación en el equipo real

1. Pruebe la API y prepare el Hotspot; confirme el mensaje de ejecución y la hora.
2. Cree una ficha de prueba, imprima el QR y compruebe el ingreso desde un teléfono conectado al SSID.
3. Desconecte la sesión y confirme que puede volver a ingresar durante su vigencia.
4. Revoque la ficha y confirme tanto el cierre de la sesión como el rechazo de nuevos ingresos.
5. Antes de entregar fichas a visitantes, verifique un vencimiento de prueba con su administrador: reserve una ficha solo para pruebas, adelante su instante de expiración en un entorno de laboratorio y confirme la ejecución del barrido y del guard de ingreso. No modifique las fechas/comentarios de fichas reales.
6. Compruebe la recuperación tras un reinicio y que NTP restaure correctamente la hora.
7. Pruebe la revocación pendiente desconectando temporalmente la comunicación de la aplicación con un router de laboratorio y ejecute la conciliación al recuperarla.

La automatización cubre PHP/Laravel, cifrado, permisos, publicación idempotente, respuestas de error de API, aislamiento y conciliación con una API simulada. No reemplaza esta verificación del protocolo y los scripts contra su firmware.

## Si la prueba de conexión funciona pero falla la preparación

La prueba básica solo necesita respuestas pequeñas. La preparación envía y lee
código de varias líneas, y ejecuta el script de comprobación. La librería PHP
1.6.0 utiliza una única lectura/escritura del socket por palabra: una respuesta
fragmentada puede quedar incompleta. El módulo usa ahora lecturas y escrituras
completas para los comandos posteriores a la autenticación y mantiene los límites
entre respuestas sucesivas. No modifica archivos de `vendor` ni requiere actualizar
Composer o la base de datos.

Los errores muestran la ruta de la operación, sin atributos, código fuente ni
contraseñas. Por ejemplo, `/system/script/add` identifica la creación y
`/system/script/run` la ejecución de la comprobación. Los rechazos de permisos,
errores de sintaxis (con línea/columna cuando RouterOS las devuelve), interrupciones
y tiempos de espera se distinguen del mensaje genérico anterior. Un fallo no
habilita nuevas fichas ni marca el router como preparado.

Después de integrar este ajuste, actualice la aplicación, ejecute
`php artisan view:clear` y vuelva a **Preparar y verificar Hotspot**. No hace falta
otra migración. Si aún falla, conserve el mensaje completo de la operación y
consulte en WinBox → Terminal:

```routeros
/user group print detail where name="gestionmtk-api"
/system scheduler print detail without-paging where name~"^gmtk-"
/log print without-paging where message~"gmtk-"
```

Las pruebas del transporte usan respuestas binarias largas entregadas en
fragmentos cortos, escritura parcial, interrupciones y mensajes de rechazo. No
sustituyen la prueba de preparación contra el router físico.

## Referencias oficiales

- [HotSpot y propiedades de usuarios](https://manual.mikrotik.com/docs/authentication-authorization-accounting/hotspot-captive-portal/)
- [Personalización del portal y parámetros de login](https://manual.mikrotik.com/docs/authentication-authorization-accounting/hotspot-captive-portal/hotspot-customisation/)
- [Lenguaje de scripts y funciones](https://manual.mikrotik.com/docs/developer-guides/scripting/)
- [Scheduler](https://help.mikrotik.com/docs/spaces/ROS/pages/40992881/Scheduler)
- [Librería PHP RouterOS utilizada por el proyecto](https://github.com/EvilFreelancer/routeros-api-php)
