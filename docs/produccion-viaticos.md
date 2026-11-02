# Viáticos en Producción

Los controladores, vistas y rutas del cálculo pertenecen a `Modules/Produccion`.
Se reutilizan las tablas `viaticos` y `ciudades` y los nombres `admin.viaticos.*`.
El menú general ya no contiene los accesos anteriores a viáticos.

## Uso

- Abrir Producción → Viáticos → Ciudades y kilometraje.
- Agregar o editar nombre y kilómetros por carretera de **ida** desde Tegucigalpa
  y/o San Pedro Sula. Vacío significa sin configurar; cero es válido.
  No se inventan distancias ni se reemplazan los datos existentes.
- En Calcular viático, seleccionar origen y ciudad. El servidor consulta la
  distancia del catálogo, sin aceptar kilómetros manipulados del navegador.
- Si el destino no está registrado, elegir **Otra ciudad**, escribir su nombre y
  kilómetros de ida. Al guardar se crean la ciudad y el viático juntos; queda
  guardado el kilometraje desde el origen seleccionado. El otro origen permanece
  sin configurar. No se requiere salir del formulario ni administrar el catálogo.
  Un nombre ya registrado se rechaza para utilizar su entrada existente.
  Al cambiar de origen se limpia el kilometraje manual para evitar reutilizar
  por error la distancia de la otra ruta.
- Indicar mano de obra como **monto total del trabajo en lempiras**. Se suma
  una sola vez; no se multiplica por días ni técnicos.
- Detalle, historial y PDF muestran mano de obra y total general.
  El PDF también muestra origen y distancia de ida.

La fórmula de combustible se conserva:

`ceil((distancia_ida_km * 2 + 30) / 40) * precio_combustible * 1.15`

Los demás cálculos conservan sus reglas anteriores. Distancia y nombre quedan
guardados en cada viático: editar el catálogo no recalcula registros históricos.
Los anteriores muestran origen no registrado y mano de obra cero;
sus totales no se recalculan.

Permisos: `admin.viaticos.index` para el módulo y, adicionalmente,
`administrar-ciudades` para crear/editar el catálogo. Asignarlos desde la
administración de roles a los usuarios que deban utilizarlos.

## Actualización en Windows / IIS

Después de integrar el PR, respaldar la base `gestionmtk` y revisar `git status`
en `C:\Web\gestionmtk` antes de actualizar. Conservar `.env` y `public/web.config`.
Aplicar durante una ventana sin creación de viáticos:

```powershell
Set-Location 'C:\Web\gestionmtk'
git pull --ff-only
composer dump-autoload --no-dev --optimize
php artisan migrate --path=database/migrations/2026_11_02_000001_add_produccion_fields_to_viaticos.php --force
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan route:list --path=produccion/admin/viaticos
```

Si un comando falla, detenerse antes de continuar.
**No ejecutar `migrate` sin `--path`**: existen otras migraciones pendientes
que deben revisarse por separado. La migración amplía `distancia_km` a decimal
en MariaDB/MySQL, agrega origen, referencia de ciudad y mano de obra, y completa
`noches`/`extra` solo si faltan. No copia ni borra viajes.

En IIS, el listado queda en `/gestionmtk/produccion/admin/viaticos`.
Detalle y PDF utilizan rutas nombradas y respetan `/gestionmtk/`.

Comprobar un viaje desde cada origen, un viaje local de cero km, una ciudad sin
distancia, los totales, el PDF y un viático histórico. Verificar también que las
liquidaciones y la agenda sigan abriendo.

## Pruebas

En una instalación de desarrollo con dependencias de Composer completas:

```text
vendor/bin/phpunit tests/Feature/ProduccionViaticosTest.php
node --test tests/Frontend/viaticos-selector.test.cjs
```

Las pruebas PHP utilizan SQLite en memoria con tablas mínimas aisladas, sin
ejecutar todas las migraciones del proyecto. Cubren permisos, rutas, selección
de origen, validaciones, importes, conservación de distancias y PDF.
Utilizar PHP 8.3, SQLite y extensiones DOM, GD, mbstring, sockets y zip.

