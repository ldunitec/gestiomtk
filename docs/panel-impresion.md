# Panel de impresión CREY

## Hallazgos y correcciones

1. Las estadísticas confundían observaciones/foto registrada con impresión
   realizada. Se agrega un estado independiente con fecha y usuario; ningún
   registro histórico se marca automáticamente como impreso.
2. El modelo utiliza `creys`, pero la migración del módulo creaba `creysnew`, sin
   ID ni curso. La actualización prepara explícitamente `creys` y agrega los
   campos nuevos sin borrar ni copiar registros a otra tabla.
3. La importación usaba `H:/Cristo rey/crey` y `dd()`, que interrumpía el proceso al
   faltar una foto. Ahora admite un origen configurable y registra alumnos sin
   foto. Reimportar el mismo DNI actualiza el registro en lugar de duplicarlo.
4. Las rutas guardar, eliminarTodos e imprimirPorCurso apuntaban a métodos
   inexistentes. Se retiran las dos primeras y se dirige el acceso PDF anterior al
   panel. Los identificadores de edición/borrado son numéricos.
5. Se agrega autenticación y el permiso existente `admin.alumnos.crey` al módulo.
   La importación requiere además `admin.alumnos.importar.crey`.
6. La edición utiliza datos validados; cambiar nombre o grupo deja el registro
   pendiente. Se corrige el modal para Bootstrap 4/AdminLTE 3.

## Uso

Abrir Anexos → Panel de impresión, o `/gestionmtk/impresion/panel`.
Aplicar filtros de curso, sección, modalidad, nombre/DNI y estado.
El filtro inicial es Pendientes. Las tarjetas resumen el grupo antes de aplicar
el filtro de estado.

- **Individual:** pulsar Imprimir individual en la fila con foto.
- **Seleccionados:** marcar alumnos de la página y preparar el lote.
- **Sección completa:** aplicar curso y sección, elegir Toda la sección filtrada.
  Incluye todas las páginas y respeta modalidad, búsqueda y estado.
- **Vista previa:** elegir tamaño en mm (por defecto 30 × 40), abrir la impresión
  del navegador y luego confirmar la impresión realizada. Cancelar o cerrar la
  vista no marca registros. La vista vence a las dos horas.
- **ZIP:** descargar fotos agrupadas como
  `CURSO/SECCION/MODALIDAD/NOMBRES APELLIDOS.jpg` (o extensión PNG/WEBP original).
  El nombre se convierte a mayúsculas y se limpian caracteres inválidos de Windows.
  Para homónimos se añade ` - ID número`. `RESUMEN.csv` enumera incluidos y sin foto.
- **Marcar como impresos al generar el ZIP** está activado por defecto. Solo se
  actualizan las fotos incluidas después de generar correctamente el archivo.
  No comprueba una impresora física ni garantiza que el navegador haya guardado
  la descarga. Desmarcar para exportar sin cambiar estados y actualizar el panel.
- Reemplazar una foto deja al alumno pendiente. Para reimprimir, filtrar Impresos
  o Todos.

Máximo 500 registros y 100 MB de fotos por ZIP. Los registros sin foto no se
marcan. Si todo el lote carece de fotos o falla el ZIP, no se registra éxito ni
se cambian estados. Los últimos lotes muestran incluidos, faltantes y marcados.

## Fotos existentes

Se reconocen estas ubicaciones heredadas sin modificar los originales:

- `public/uploads/Crey/Estudiantes/CURSO/MODALIDAD/SECCION/NOMBRES_APELLIDOS.jpg`.
- `storage/app/public/carnetizacion/crey/CODIGO.jpg`, donde CODIGO corresponde a
  observaciones, incluyendo el enlace `public/storage`.

Las nuevas fotos se guardan en `storage/app/impresion/fotos` y se sirven mediante
una ruta autenticada. Al migrar es necesario copiar fotos además de la base de
datos: un código de foto no significa que exista el archivo.

Para copiar originales durante la importación Excel, configurar
`IMPRESION_FOTOS_ORIGEN` con la carpeta real del servidor. Es opcional; no hace
falta una unidad H: si se cargan desde el panel. Se aceptan imágenes JPG, PNG o
WEBP reales, hasta 10 MB por carga.

Si Excel tiene nombres y apellidos separados, usar encabezados `nombres` y
`apellidos`. El encabezado antiguo `nombre` utiliza una separación heurística
(últimas dos palabras como apellidos); revisar nombres compuestos.

## Actualización Windows / IIS

Respaldar `gestionmtk`, comprobar `git status` y aplicar el PR integrado durante
una ventana de mantenimiento. Conservar `.env` y `public/web.config`.
En `C:\Web\gestionmtk`:

```powershell
git pull --ff-only
composer dump-autoload --no-dev --optimize
php artisan migrate --path=database/migrations/2026_11_02_000002_add_control_impresion_to_creys.php --force
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan route:list --path=impresion
```

Detenerse si falla un comando. Ejecutar únicamente esta migración; no todas las
pendientes ni la antigua de `creysnew`. La cuenta IIS necesita lectura/escritura
en `storage`, como se configuró previamente. Verificar PHP GD y ZIP.

La actualización cubre `Modules/Impresion` y su catálogo CREY. Los módulos
separados de credenciales, diseños de carnets y alumnos LCOAR no se fusionan ni
se convierten automáticamente.

## Validación

```text
vendor/bin/phpunit tests/Feature/PanelImpresionTest.php
node --check public/js/impresion-panel.js
```

Las pruebas usan SQLite en memoria y fotos sintéticas: permisos, filtros,
homónimos, ZIP y estados, cancelación, confirmación, foto cambiada, cargas
inválidas, rutas fuera de carpeta y reimportaciones.
Validar además en IIS/MariaDB y realizar una impresión a escala 100%.
