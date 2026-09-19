
// ============================================================
// SISTEMA DE INVENTARIO DE HERRAMIENTAS - MEGATK
// Google Apps Script + Google Sheets + Google Drive
// ============================================================


// ============================================================
// CONFIGURACIÓN
// ============================================================

const SPREADSHEET_ID =
  "1bxZnHlCKTviQC-x-cXzL2b6HGs_QmljOIv2fCPlKrQw";

const FOTO_HERRAMIENTAS_FOLDER_ID =
  "1FAAoIXaqCRpQb-cLt-ZHhz3bC4zJ__Bs";

const FOTO_CHEQUEOS_FOLDER_ID =
  "1v7bV0Dwq5x2COCW2wFfBs9lJL6kStX_l";


// ============================================================
// WEB APP
// ============================================================

function doGet() {

  return HtmlService
    .createHtmlOutputFromFile("index")
    .setTitle("Inventario de Herramientas MEGATK")
    .setXFrameOptionsMode(
      HtmlService.XFrameOptionsMode.ALLOWALL
    )
    .addMetaTag(
      "viewport",
      "width=device-width, initial-scale=1"
    );

}


// ============================================================
// CONEXIÓN GOOGLE SHEETS
// ============================================================

function obtenerSpreadsheet() {

  if (
    !SPREADSHEET_ID ||
    String(SPREADSHEET_ID).trim() === ""
  ) {

    throw new Error(
      "SPREADSHEET_ID no está configurado."
    );

  }


  try {

    const ss =
      SpreadsheetApp.openById(
        SPREADSHEET_ID
      );


    if (!ss) {

      throw new Error(
        "No se pudo abrir el Spreadsheet."
      );

    }


    return ss;


  } catch (error) {

    throw new Error(
      "Error al abrir Google Sheets: " +
      error.message
    );

  }

}


// ============================================================
// PROBAR CONEXIÓN
// ============================================================

function probarConexion() {

  try {

    const ss =
      obtenerSpreadsheet();


    Logger.log(
      "================================"
    );

    Logger.log(
      "CONEXIÓN CORRECTA"
    );

    Logger.log(
      "Nombre: " +
      ss.getName()
    );

    Logger.log(
      "ID: " +
      ss.getId()
    );

    Logger.log(
      "URL: " +
      ss.getUrl()
    );

    Logger.log(
      "Hojas:"
    );


    ss.getSheets().forEach(
      function(hoja) {

        Logger.log(
          "- " +
          hoja.getName()
        );

      }
    );


    Logger.log(
      "================================"
    );


    return {

      success: true,

      nombre:
        ss.getName(),

      id:
        ss.getId(),

      url:
        ss.getUrl(),

      hojas:
        ss.getSheets().map(
          function(hoja) {

            return hoja.getName();

          }
        )

    };


  } catch (error) {

    Logger.log(
      "ERROR: " +
      error.message
    );

    throw error;

  }

}


// ============================================================
// CARGAR DATOS INICIALES
// ============================================================

function cargarDatosIniciales() {

  const ss =
    obtenerSpreadsheet();


  // ==========================================================
  // EMPLEADOS
  // ==========================================================

  let hojaEmpleados =
    ss.getSheetByName(
      "Empleados"
    );


  if (!hojaEmpleados) {

    hojaEmpleados =
      ss.insertSheet(
        "Empleados"
      );


    hojaEmpleados.appendRow([
      "ID",
      "Nombre",
      "DNI",
      "Cargo",
      "Telefono",
      "Estado"
    ]);

  }


  // ==========================================================
  // HERRAMIENTAS
  // ==========================================================

  let hojaHerramientas =
    ss.getSheetByName(
      "Herramientas"
    );


  if (!hojaHerramientas) {

    hojaHerramientas =
      ss.insertSheet(
        "Herramientas"
      );


    hojaHerramientas.appendRow([
      "ID",
      "Codigo",
      "Nombre",
      "Descripcion",
      "Cantidad",
      "Estado",
      "FotoURL"
    ]);

  }


  // ==========================================================
  // ASIGNACIONES
  // ==========================================================

  let hojaAsignaciones =
    ss.getSheetByName(
      "Asignaciones"
    );


  if (!hojaAsignaciones) {

    hojaAsignaciones =
      ss.insertSheet(
        "Asignaciones"
      );


    hojaAsignaciones.appendRow([
      "ID",
      "Fecha",
      "EmpleadoID",
      "EmpleadoNombre",
      "HerramientaID",
      "Codigo",
      "Herramienta",
      "Cantidad",
      "Estado",
      "FechaDevolucion",
      "Observacion"
    ]);

  }


  // ==========================================================
  // HISTORIAL
  // ==========================================================

  let hojaHistorial =
    ss.getSheetByName(
      "Historial"
    );


  if (!hojaHistorial) {

    hojaHistorial =
      ss.insertSheet(
        "Historial"
      );


    hojaHistorial.appendRow([
      "Fecha",
      "Mes",
      "EmpleadoID",
      "EmpleadoNombre",
      "HerramientaID",
      "Codigo",
      "Herramienta",
      "Estado",
      "Observacion",
      "FotoChequeoURL"
    ]);

  }


  // ==========================================================
  // LEER DATOS
  // ==========================================================

  const empleados =
    obtenerRegistrosComoObjeto(
      hojaEmpleados
    );


  const herramientas =
    obtenerRegistrosComoObjeto(
      hojaHerramientas
    );


  const asignaciones =
    obtenerRegistrosComoObjeto(
      hojaAsignaciones
    );


  const datosHistorial =
    obtenerRegistrosComoObjeto(
      hojaHistorial
    );


  // ==========================================================
  // FECHA
  // ==========================================================

  const ahora =
    new Date();


  const zona =
    Session.getScriptTimeZone();


  const mesActual =
    Utilities.formatDate(
      ahora,
      zona,
      "yyyy-MM"
    );


  // ==========================================================
  // ESTADÍSTICAS
  // ==========================================================

  const inventariosMes =
    datosHistorial.filter(
      function(h) {

        return String(
          h.Mes || ""
        ) ===
        String(
          mesActual
        );

      }
    ).length;


  const empleadosActivos =
    empleados.filter(
      function(e) {

        return String(
          e.Estado || ""
        )
          .trim()
          .toUpperCase() !==
          "INACTIVO";

      }
    ).length;


  return {

    empleados:
      empleados,

    herramientas:
      herramientas,

    asignaciones:
      asignaciones,

    mesActual:
      mesActual,

    estadisticas: {

      empleados:
        empleadosActivos,

      herramientas:
        herramientas.length,

      inventariosMes:
        inventariosMes,

      mes:
        Utilities.formatDate(
          ahora,
          zona,
          "MMMM yyyy"
        )

    }

  };

}


// ============================================================
// GUARDAR HERRAMIENTA
// ============================================================

function guardarHerramienta(datos) {

  if (!datos) {

    throw new Error(
      "No se recibieron los datos de la herramienta."
    );

  }


  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Herramientas"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Herramientas."
    );

  }


  let fotoUrl =
    "";


  // ==========================================================
  // GUARDAR FOTO
  // ==========================================================

  if (datos.fotoBase64) {

    const nombreArchivo =
      "Herramienta_" +
      (datos.codigo || "S_N") +
      "_" +
      new Date().getTime() +
      ".jpg";


    fotoUrl =
      guardarImagenEnDrive(
        datos.fotoBase64,
        nombreArchivo,
        FOTO_HERRAMIENTAS_FOLDER_ID
      );

  }


  // ==========================================================
  // ID
  // ==========================================================

  const id =
    "H-" +
    new Date().getTime();


  // ==========================================================
  // GUARDAR
  // ==========================================================

  hoja.appendRow([

    id,

    datos.codigo ||
      id,

    datos.nombre ||
      "",

    datos.descripcion ||
      "",

    Number(
      datos.cantidad
    ) || 1,

    "ACTIVA",

    fotoUrl

  ]);


  return {

    success:
      true,

    id:
      id,

    mensaje:
      "Herramienta guardada correctamente."

  };

}


// ============================================================
// ASIGNAR HERRAMIENTA A TÉCNICO
// ============================================================

function guardarAsignacion(datos) {

  if (!datos) {

    throw new Error(
      "No se recibieron los datos de la asignación."
    );

  }


  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Asignaciones"
    );


  const hojaEmpleados =
    ss.getSheetByName(
      "Empleados"
    );


  const hojaHerramientas =
    ss.getSheetByName(
      "Herramientas"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Asignaciones."
    );

  }


  if (!hojaEmpleados) {

    throw new Error(
      "No existe la hoja Empleados."
    );

  }


  if (!hojaHerramientas) {

    throw new Error(
      "No existe la hoja Herramientas."
    );

  }


  const empleados =
    obtenerRegistrosComoObjeto(
      hojaEmpleados
    );


  const herramientas =
    obtenerRegistrosComoObjeto(
      hojaHerramientas
    );


  const asignaciones =
    obtenerRegistrosComoObjeto(
      hoja
    );


  // ==========================================================
  // BUSCAR EMPLEADO
  // ==========================================================

  const empleado =
    empleados.find(
      function(e) {

        return normalizar(
          e.ID
        ) ===
        normalizar(
          datos.empleadoId
        );

      }
    );


  // ==========================================================
  // BUSCAR HERRAMIENTA
  // ==========================================================

  const herramienta =
    herramientas.find(
      function(h) {

        return normalizar(
          h.ID
        ) ===
        normalizar(
          datos.herramientaId
        );

      }
    );


  if (!empleado) {

    throw new Error(
      "El técnico seleccionado no existe."
    );

  }


  if (
    normalizar(
      empleado.Estado
    ) ===
    "INACTIVO"
  ) {

    throw new Error(
      "El técnico seleccionado está inactivo."
    );

  }


  if (!herramienta) {

    throw new Error(
      "La herramienta seleccionada no existe."
    );

  }


  // ==========================================================
  // CANTIDAD
  // ==========================================================

  const cantidad =
    Number(
      datos.cantidad
    ) || 0;


  if (cantidad < 1) {

    throw new Error(
      "La cantidad debe ser mayor que cero."
    );

  }


  // ==========================================================
  // CALCULAR CANTIDAD ASIGNADA
  // ==========================================================

  const cantidadAsignada =
    asignaciones
      .filter(
        function(a) {

          return (

            normalizar(
              a.HerramientaID
            ) ===
            normalizar(
              datos.herramientaId
            )

            &&

            normalizar(
              a.Estado
            ) ===
            "ASIGNADA"

          );

        }
      )
      .reduce(
        function(total, a) {

          return total +
            (
              Number(
                a.Cantidad
              ) || 0
            );

        },
        0
      );


  const cantidadTotal =
    Number(
      herramienta.Cantidad
    ) || 0;


  const disponible =
    cantidadTotal -
    cantidadAsignada;


  if (cantidad > disponible) {

    throw new Error(
      "Cantidad insuficiente. Disponible: " +
      Math.max(
        0,
        disponible
      )
    );

  }


  // ==========================================================
  // EVITAR DUPLICAR ASIGNACIÓN
  // ==========================================================

  const existenteIndex =
    asignaciones.findIndex(
      function(a) {

        return (

          normalizar(
            a.EmpleadoID
          ) ===
          normalizar(
            datos.empleadoId
          )

          &&

          normalizar(
            a.HerramientaID
          ) ===
          normalizar(
            datos.herramientaId
          )

          &&

          normalizar(
            a.Estado
          ) ===
          "ASIGNADA"

        );

      }
    );


  const fecha =
    Utilities.formatDate(
      new Date(),
      Session.getScriptTimeZone(),
      "yyyy-MM-dd HH:mm:ss"
    );


  // ==========================================================
  // ACTUALIZAR ASIGNACIÓN EXISTENTE
  // ==========================================================

  if (
    existenteIndex !== -1
  ) {

    const fila =
      existenteIndex + 2;


    const cantidadAnterior =
      Number(
        hoja
          .getRange(
            fila,
            8
          )
          .getValue()
      ) || 0;


    hoja
      .getRange(
        fila,
        8
      )
      .setValue(
        cantidadAnterior +
        cantidad
      );


    return {

      success:
        true,

      mensaje:
        "La asignación existente fue actualizada."

    };

  }


  // ==========================================================
  // NUEVA ASIGNACIÓN
  // ==========================================================

  const id =
    "ASG-" +
    new Date().getTime();


  hoja.appendRow([

    id,

    fecha,

    empleado.ID,

    empleado.Nombre ||
      "",

    herramienta.ID,

    herramienta.Codigo ||
      "",

    herramienta.Nombre ||
      "",

    cantidad,

    "ASIGNADA",

    "",

    datos.observacion ||
      ""

  ]);


  return {

    success:
      true,

    id:
      id,

    mensaje:
      "Herramienta asignada correctamente."

  };

}


// ============================================================
// REGISTRAR CHEQUEO DE INVENTARIO
// ============================================================

function registrarChequeoInventarioCompleto(
  datosChequeo
) {

  if (!datosChequeo) {

    throw new Error(
      "No se recibieron los datos del chequeo."
    );

  }


  const ss =
    obtenerSpreadsheet();


  const hojaHistorial =
    ss.getSheetByName(
      "Historial"
    );


  const hojaEmpleados =
    ss.getSheetByName(
      "Empleados"
    );


  if (!hojaHistorial) {

    throw new Error(
      "No existe la hoja Historial."
    );

  }


  if (!hojaEmpleados) {

    throw new Error(
      "No existe la hoja Empleados."
    );

  }


  // ==========================================================
  // EMPLEADOS
  // ==========================================================

  const empleados =
    obtenerRegistrosComoObjeto(
      hojaEmpleados
    );


  const emp =
    empleados.find(
      function(e) {

        return normalizar(
          e.ID
        ) ===
        normalizar(
          datosChequeo.empleadoId
        );

      }
    );


  if (!emp) {

    throw new Error(
      "No se encontró el empleado seleccionado."
    );

  }


  const empleadoNombre =
    emp.Nombre ||
    "Desconocido";


  // ==========================================================
  // VALIDAR ITEMS
  // ==========================================================

  if (
    !datosChequeo.items ||
    !Array.isArray(
      datosChequeo.items
    )
  ) {

    throw new Error(
      "No se recibieron los elementos del inventario."
    );

  }


  if (
    datosChequeo.items.length === 0
  ) {

    throw new Error(
      "No hay herramientas para registrar en el chequeo."
    );

  }


  // ==========================================================
  // FOTO GENERAL
  // ==========================================================

  let fotoChequeoUrl =
    "";


  if (
    datosChequeo.fotoGeneralBase64
  ) {

    const nombreFoto =
      "Chequeo_" +
      limpiarNombreArchivo(
        empleadoNombre
      ) +
      "_" +
      (
        datosChequeo.mes ||
        "SIN_MES"
      ) +
      "_" +
      new Date().getTime() +
      ".jpg";


    fotoChequeoUrl =
      guardarImagenEnDrive(
        datosChequeo.fotoGeneralBase64,
        nombreFoto,
        FOTO_CHEQUEOS_FOLDER_ID
      );

  }


  // ==========================================================
  // FECHA
  // ==========================================================

  const fechaHoy =
    Utilities.formatDate(
      new Date(),
      Session.getScriptTimeZone(),
      "yyyy-MM-dd HH:mm:ss"
    );


  // ==========================================================
  // GUARDAR HISTORIAL
  // ==========================================================

  datosChequeo.items.forEach(
    function(item) {

      hojaHistorial.appendRow([

        fechaHoy,

        datosChequeo.mes ||
          "",

        datosChequeo.empleadoId ||
          "",

        empleadoNombre,

        item.herramientaId ||
          "",

        item.codigo ||
          "",

        item.nombreHerramienta ||
          "",

        item.estado ||
          "OK",

        item.observacion ||
          "",

        fotoChequeoUrl

      ]);

    }
  );


  return {

    success:
      true,

    mensaje:
      "Inventario y foto general guardados con éxito."

  };

}


// ============================================================
// DEVOLVER HERRAMIENTA
// ============================================================

function devolverAsignacion(id) {

  if (!id) {

    throw new Error(
      "No se recibió el ID de la asignación."
    );

  }


  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Asignaciones"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Asignaciones."
    );

  }


  const registros =
    obtenerRegistrosComoObjeto(
      hoja
    );


  const index =
    registros.findIndex(
      function(a) {

        return normalizar(
          a.ID
        ) ===
        normalizar(
          id
        );

      }
    );


  if (index === -1) {

    throw new Error(
      "No se encontró la asignación."
    );

  }


  const fila =
    index + 2;


  const estadoActual =
    normalizar(
      hoja
        .getRange(
          fila,
          9
        )
        .getValue()
    );


  if (
    estadoActual ===
    "DEVUELTA"
  ) {

    return {

      success:
        true,

      mensaje:
        "La herramienta ya había sido devuelta."

    };

  }


  hoja
    .getRange(
      fila,
      9
    )
    .setValue(
      "DEVUELTA"
    );


  hoja
    .getRange(
      fila,
      10
    )
    .setValue(
      Utilities.formatDate(
        new Date(),
        Session.getScriptTimeZone(),
        "yyyy-MM-dd HH:mm:ss"
      )
    );


  return {

    success:
      true,

    mensaje:
      "Devolución registrada correctamente."

  };

}


// ============================================================
// GUARDAR IMAGEN EN GOOGLE DRIVE
// ============================================================

function guardarImagenEnDrive(
  base64Data,
  fileName,
  folderId
) {

  try {

    if (!base64Data) {

      return "";

    }


    const partes =
      String(
        base64Data
      ).split(",");


    if (
      partes.length < 2
    ) {

      throw new Error(
        "Formato Base64 de imagen inválido."
      );

    }


    const match =
      partes[0].match(
        /data:(.*?);base64/i
      );


    if (!match) {

      throw new Error(
        "No se pudo determinar el tipo de imagen."
      );

    }


    const contentType =
      match[1];


    const bytes =
      Utilities.base64Decode(
        partes[1]
      );


    const blob =
      Utilities.newBlob(
        bytes,
        contentType,
        fileName
      );


    let folder;


    if (
      folderId &&
      String(
        folderId
      ).trim() !== ""
    ) {

      folder =
        DriveApp.getFolderById(
          folderId
        );

    } else {

      folder =
        DriveApp.getRootFolder();

    }


    const archivo =
      folder.createFile(
        blob
      );


    // Compartir únicamente si es posible
    try {

      archivo.setSharing(
        DriveApp.Access.ANYONE_WITH_LINK,
        DriveApp.Permission.VIEW
      );

    } catch (sharingError) {

      Logger.log(
        "No se pudo establecer acceso público: " +
        sharingError.message
      );

    }


    return archivo.getUrl();


  } catch (error) {

    Logger.log(
      "Error al subir imagen: " +
      error.message
    );


    throw new Error(
      "No se pudo guardar la imagen: " +
      error.message
    );

  }

}


// ============================================================
// CONVERTIR FILAS A OBJETOS
// ============================================================

function obtenerRegistrosComoObjeto(
  hoja
) {

  if (!hoja) {

    return [];

  }


  const rows =
    hoja
      .getDataRange()
      .getValues();


  if (
    rows.length <= 1
  ) {

    return [];

  }


  const headers =
    rows[0].map(
      function(header) {

        return String(
          header
        ).trim();

      }
    );


  const data =
    rows.slice(1);


  return data.map(
    function(row) {

      const obj = {};


      headers.forEach(
        function(header, index) {

          if (
            !header
          ) {

            return;

          }


          let valor =
            row[index];


          // ----------------------------------------
          // CONVERTIR FECHAS A TEXTO
          // ----------------------------------------

          if (
            valor instanceof Date
          ) {

            valor =
              Utilities.formatDate(
                valor,
                Session.getScriptTimeZone(),
                "yyyy-MM-dd HH:mm:ss"
              );

          }


          // ----------------------------------------
          // EVITAR NULL / UNDEFINED
          // ----------------------------------------

          if (
            valor === null ||
            valor === undefined
          ) {

            valor = "";

          }


          obj[header] =
            valor;

        }
      );


      return obj;

    }
  );

}


// ============================================================
// NORMALIZAR VALORES
// ============================================================

function normalizar(
  valor
) {

  if (
    valor === null ||
    valor === undefined
  ) {

    return "";

  }


  return String(
    valor
  )
    .trim()
    .toUpperCase();

}


// ============================================================
// LIMPIAR NOMBRE DE ARCHIVO
// ============================================================

function limpiarNombreArchivo(
  nombre
) {

  return String(
    nombre || "SIN_NOMBRE"
  )
    .trim()
    .replace(
      /[\\\/:*?"<>|#%]/g,
      "_"
    )
    .replace(
      /\s+/g,
      "_"
    );

}


// ============================================================
// INFORMACIÓN DEL EMPLEADO
// ============================================================

function obtenerInfoEmpleado(
  id
) {

  try {

    // ========================================================
    // VALIDAR ID
    // ========================================================

    if (
      id === null ||
      id === undefined ||
      String(id).trim() === ""
    ) {

      throw new Error(
        "No se recibió el ID del empleado."
      );

    }


    const ss =
      obtenerSpreadsheet();


    const hojaEmpleados =
      ss.getSheetByName(
        "Empleados"
      );


    const hojaHistorial =
      ss.getSheetByName(
        "Historial"
      );


    const hojaAsignaciones =
      ss.getSheetByName(
        "Asignaciones"
      );


    // ========================================================
    // VALIDAR HOJAS
    // ========================================================

    if (!hojaEmpleados) {

      throw new Error(
        "No existe la hoja Empleados."
      );

    }


    if (!hojaHistorial) {

      throw new Error(
        "No existe la hoja Historial."
      );

    }


    // ========================================================
    // LEER EMPLEADOS
    // ========================================================

    const empleados =
      obtenerRegistrosComoObjeto(
        hojaEmpleados
      );


    Logger.log(
      "ID recibido: [" +
      id +
      "]"
    );


    Logger.log(
      "Cantidad de empleados: " +
      empleados.length
    );


    // ========================================================
    // BUSCAR EMPLEADO
    // ========================================================

    const empleado =
      empleados.find(
        function(e) {

          return normalizar(
            e.ID
          ) ===
          normalizar(
            id
          );

        }
      );


    // ========================================================
    // SI NO EXISTE
    // ========================================================

    if (!empleado) {

      Logger.log(
        "Empleado no encontrado. IDs disponibles:"
      );


      empleados.forEach(
        function(e) {

          Logger.log(
            "[" +
            e.ID +
            "] - " +
            e.Nombre
          );

        }
      );


      return {

        empleado:
          null,

        ultimoInventario: {

          existe:
            false

        },

        chequeadas:
          [],

        asignaciones:
          []

      };

    }


    // ========================================================
    // HISTORIAL
    // ========================================================

    const historial =
      obtenerRegistrosComoObjeto(
        hojaHistorial
      );


    const historialEmpleado =
      historial.filter(
        function(h) {

          return normalizar(
            h.EmpleadoID
          ) ===
          normalizar(
            id
          );

        }
      );


    // ========================================================
    // MES ACTUAL
    // ========================================================

    const ahora =
      new Date();


    const zona =
      Session.getScriptTimeZone();


    const mesActual =
      Utilities.formatDate(
        ahora,
        zona,
        "yyyy-MM"
      );


    // ========================================================
    // CHEQUEADAS ESTE MES
    // ========================================================

    const chequeadasMes =
      historialEmpleado.filter(
        function(h) {

          return String(
            h.Mes || ""
          ).trim() ===
          String(
            mesActual
          ).trim();

        }
      );


    // ========================================================
    // ÚLTIMO INVENTARIO
    // ========================================================

    let ultimoInventario = {

      existe:
        false

    };


    if (
      historialEmpleado.length > 0
    ) {

      // Buscar el registro más reciente
      // por fecha

      const historialOrdenado =
        historialEmpleado
          .slice()
          .sort(
            function(a, b) {

              const fechaA =
                convertirFecha(
                  a.Fecha
                );

              const fechaB =
                convertirFecha(
                  b.Fecha
                );

              return (
                fechaB -
                fechaA
              );

            }
          );


      const ultimo =
        historialOrdenado[0];


      ultimoInventario = {

        existe:
          true,

        fecha:
          String(
            ultimo.Fecha || ""
          ),

        mes:
          String(
            ultimo.Mes || ""
          )

      };

    }


    // ========================================================
    // ASIGNACIONES
    // ========================================================

    let asignacionesEmpleado =
      [];


    if (
      hojaAsignaciones
    ) {

      const asignaciones =
        obtenerRegistrosComoObjeto(
          hojaAsignaciones
        );


      asignacionesEmpleado =
        asignaciones.filter(
          function(a) {

            return normalizar(
              a.EmpleadoID
            ) ===
            normalizar(
              id
            );

          }
        );


      // Convertir fechas y valores
      // para enviarlos correctamente al HTML

      asignacionesEmpleado =
        asignacionesEmpleado.map(
          function(a) {

            const copia = {};


            Object.keys(
              a
            ).forEach(
              function(campo) {

                let valor =
                  a[campo];


                if (
                  valor instanceof Date
                ) {

                  valor =
                    Utilities.formatDate(
                      valor,
                      zona,
                      "yyyy-MM-dd HH:mm:ss"
                    );

                }


                if (
                  valor === null ||
                  valor === undefined
                ) {

                  valor = "";

                }


                copia[campo] =
                  valor;

              }
            );


            return copia;

          }
        );

    }


    // ========================================================
    // RESULTADO
    // ========================================================

    return {

      empleado:
        empleado,

      ultimoInventario:
        ultimoInventario,

      chequeadas:
        chequeadasMes,

      asignaciones:
        asignacionesEmpleado

    };


  } catch (error) {

    Logger.log(
      "================================"
    );


    Logger.log(
      "ERROR EN obtenerInfoEmpleado"
    );


    Logger.log(
      error.message
    );


    Logger.log(
      error.stack
    );


    Logger.log(
      "================================"
    );


    throw new Error(
      "Error al obtener empleado: " +
      error.message
    );

  }

}


// ============================================================
// CONVERTIR FECHA
// ============================================================

function convertirFecha(
  valor
) {

  if (
    valor instanceof Date
  ) {

    return valor.getTime();

  }


  if (
    !valor
  ) {

    return 0;

  }


  const fecha =
    new Date(
      valor
    );


  if (
    isNaN(
      fecha.getTime()
    )
  ) {

    return 0;

  }


  return fecha.getTime();

}


// ============================================================
// GUARDAR EMPLEADO
// ============================================================

function guardarEmpleado(
  datos
) {

  if (!datos) {

    throw new Error(
      "No se recibieron los datos del empleado."
    );

  }


  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Empleados"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Empleados."
    );

  }


  const nombre =
    String(
      datos.nombre || ""
    ).trim();


  if (
    nombre === ""
  ) {

    throw new Error(
      "El nombre del empleado es obligatorio."
    );

  }


  const id =
    "EMP-" +
    new Date().getTime();


  hoja.appendRow([

    id,

    nombre,

    datos.dni ||
      "",

    datos.cargo ||
      "",

    datos.telefono ||
      "",

    "ACTIVO"

  ]);


  return {

    success:
      true,

    id:
      id,

    mensaje:
      "Empleado guardado correctamente."

  };

}


// ============================================================
// OBTENER HISTORIAL FILTRADO
// ============================================================

function obtenerHistorialFiltrado(
  empleadoId
) {

  const ss =
    obtenerSpreadsheet();


  const hojaHistorial =
    ss.getSheetByName(
      "Historial"
    );


  if (!hojaHistorial) {

    throw new Error(
      "No existe la hoja Historial."
    );

  }


  let historial =
    obtenerRegistrosComoObjeto(
      hojaHistorial
    );


  // ==========================================================
  // FILTRAR POR EMPLEADO
  // ==========================================================

  if (
    empleadoId !== null &&
    empleadoId !== undefined &&
    String(
      empleadoId
    ).trim() !== ""
  ) {

    historial =
      historial.filter(
        function(h) {

          return normalizar(
            h.EmpleadoID
          ) ===
          normalizar(
            empleadoId
          );

        }
      );

  }


  // ==========================================================
  // MÁS RECIENTE PRIMERO
  // ==========================================================

  historial.sort(
    function(a, b) {

      return (
        convertirFecha(
          b.Fecha
        ) -
        convertirFecha(
          a.Fecha
        )
      );

    }
  );


  return historial;

}


// ============================================================
// OBTENER TODOS LOS EMPLEADOS
// ============================================================

function obtenerEmpleados() {

  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Empleados"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Empleados."
    );

  }


  return obtenerRegistrosComoObjeto(
    hoja
  );

}


// ============================================================
// OBTENER TODAS LAS HERRAMIENTAS
// ============================================================

function obtenerHerramientas() {

  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Herramientas"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Herramientas."
    );

  }


  return obtenerRegistrosComoObjeto(
    hoja
  );

}


// ============================================================
// OBTENER ASIGNACIONES
// ============================================================

function obtenerAsignaciones() {

  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Asignaciones"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Asignaciones."
    );

  }


  return obtenerRegistrosComoObjeto(
    hoja
  );

}


// ============================================================
// OBTENER HISTORIAL COMPLETO
// ============================================================

function obtenerHistorial() {

  const ss =
    obtenerSpreadsheet();


  const hoja =
    ss.getSheetByName(
      "Historial"
    );


  if (!hoja) {

    throw new Error(
      "No existe la hoja Historial."
    );

  }


  return obtenerRegistrosComoObjeto(
    hoja
  );

}


// ============================================================
// FIN DEL SISTEMA
// ============================================================

