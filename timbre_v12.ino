#include <WiFi.h>

#include <WebServer.h>

#include <Preferences.h>

#include <ESP32Time.h>

#include <Wire.h>

#include "RTClib.h"

#include "time.h"

// ================= ESTRUCTURAS Y OBJETOS =================
struct Horario {
  int hora;
  int minuto;
};

WebServer server(80);
Preferences prefs;
ESP32Time rtcInternal(0); // RTC Interno (para manejo rápido)
RTC_DS3231 rtcExternal;  // RTC Físico (Persistencia real)

// ================= PINES Y ESTADOS =================
const int timbrePin = 13;
const int ledPin = 2;
const int ledPowerPin = 14;  // LED que indica que el ESP32 está encendido
const int ledAPPin = 26;   // LED para el estado del WiFi y modo AP
const int ledWifiPin = 25;   // Pin 35: Solo enciende si hay WiFi (Fijo)

bool solicitarReinicio = false;
unsigned long tiempoReinicio = 0;
bool timbreActivo = false;
unsigned long tiempoInicioTimbre = 0;
int duracionTimbre = 5;
String nombreEscuela = "TU NOMBRE";  // Variable para el título

// Variables de Red y Tiempo
const char* ntpServer = "pool.ntp.org";
const long gmtOffset_sec = -21600; // Honduras UTC-6
const int daylightOffset_sec = 0;

const char* ap_ssid = "Timbre";
const char* ap_pass = "Leon9509";
String www_username, www_password;
bool modoAP = false;
unsigned long ultimoIntentoWiFi = 0;
const unsigned long intervaloReconexion = 30000;

// Horarios
Horario hLunes[30], hSemana[30], hNoche[30];
int tLunes = 0, tSemana = 0, tNoche = 0;
int ultimoMinuto = -1;

// ================= FUNCIONES DE TIEMPO Y RTC =================


void sincronizarHora() {
  configTime(gmtOffset_sec, daylightOffset_sec, ntpServer);
  
  struct tm timeinfo;
  if (getLocalTime(&timeinfo)) {
    Serial.println("✅ Hora NTP obtenida");
    
    // Sincronizar RTC Externo con la hora local ya ajustada
    rtcExternal.adjust(DateTime(timeinfo.tm_year + 1900, timeinfo.tm_mon + 1, timeinfo.tm_mday,
                               timeinfo.tm_hour, timeinfo.tm_min, timeinfo.tm_sec));
                               
    // Sincronizar RTC Interno
    rtcInternal.setTimeStruct(timeinfo);
  } else {
    Serial.println("⚠️ Usando RTC físico...");
    DateTime now = rtcExternal.now();
    // Forzamos al RTC interno a tomar lo que tiene el físico
    rtcInternal.setTime(now.second(), now.minute(), now.hour(), now.day(), now.month(), now.year());
  }
}
// ================= FUNCIONES DE TIMBRE =================

void activarTimbre() {
  if (!timbreActivo) {
    digitalWrite(timbrePin, HIGH);
    digitalWrite(ledPin, HIGH);
    tiempoInicioTimbre = millis();
    timbreActivo = true;
    Serial.println("🔔 Timbre Sonando...");
  }
}

void actualizarTimbre() {
  if (timbreActivo && (millis() - tiempoInicioTimbre >= (duracionTimbre * 1000))) {
    digitalWrite(timbrePin, LOW);
    digitalWrite(ledPin, LOW);
    timbreActivo = false;
    Serial.println("🔕 Timbre Apagado.");
    delay(5000);
  }
}

// ================= PERSISTENCIA NVS =================

void guardarHorarios(String tipo) {
  String datos = "";
  int total = (tipo == "lunes") ? tLunes : (tipo == "semana" ? tSemana : tNoche);
  Horario* arr = (tipo == "lunes") ? hLunes : (tipo == "semana" ? hSemana : hNoche);
  for (int i = 0; i < total; i++) {
    datos += String(arr[i].hora) + ":" + String(arr[i].minuto) + (i < total - 1 ? "," : "");
  }
  prefs.putString(tipo.c_str(), datos);
}

void cargarHorarios() {
  String tipos[] = { "lunes", "semana", "noche" };
  tLunes = 0;
  tSemana = 0;
  tNoche = 0;
  for (String t : tipos) {
    String data = prefs.getString(t.c_str(), "");
    if (data == "") continue;
    int pos = 0;
    while (data.length() > 0) {
      int coma = data.indexOf(',');
      String item = (coma == -1) ? data : data.substring(0, coma);
      int puntos = item.indexOf(':');
      int h = item.substring(0, puntos).toInt();
      int m = item.substring(puntos + 1).toInt();
      if (t == "lunes" && tLunes < 30) {
        hLunes[tLunes].hora = h;
        hLunes[tLunes++].minuto = m;
      } else if (t == "semana" && tSemana < 30) {
        hSemana[tSemana].hora = h;
        hSemana[tSemana++].minuto = m;
      } else if (t == "noche" && tNoche < 30) {
        hNoche[tNoche].hora = h;
        hNoche[tNoche++].minuto = m;
      }
      if (coma == -1) break;
      data = data.substring(coma + 1);
    }
  }
}

// ================= SEGURIDAD Y RED =================

bool auth() {
  if (!server.authenticate(www_username.c_str(), www_password.c_str())) {
    server.requestAuthentication();
    return false;
  }
  return true;
}

void conectarWiFi() {
  String ssid = prefs.getString("wifi_ssid", "Iphone");
  String pass = prefs.getString("wifi_pass", "12365412");
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid.c_str(), pass.c_str());

  int intentos = 0;
  while (WiFi.status() != WL_CONNECTED && intentos < 15) {
    delay(500);
    Serial.print(".");
    intentos++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✅ WiFi Conectado!");
    sincronizarHora();
    modoAP = false;
  } else {
    WiFi.mode(WIFI_AP);
    WiFi.softAP(ap_ssid, ap_pass);
    modoAP = true;
    Serial.println("\n⚠️ Modo AP Activado (192.168.4.1)");
    // Sincronizar desde RTC físico ya que no hay WiFi
    DateTime now = rtcExternal.now();
    rtcInternal.setTime(now.second(), now.minute(), now.hour(), now.day(), now.month(), now.year());
  }
}
void actualizarLedsEstado() {
  digitalWrite(ledPowerPin, HIGH);  // Siempre encendido

  if (modoAP) {
    // Parpadeo Rápido (cada 250ms) para indicar que es un Punto de Acceso
    digitalWrite(ledWifiPin, (millis() % 500 < 250));
  } else {
    if (WiFi.status() == WL_CONNECTED) {
      digitalWrite(ledWifiPin, HIGH);  // Fijo si hay internet
    } else {
      // Parpadeo Lento (cada 1000ms) buscando red
      digitalWrite(ledWifiPin, (millis() % 2000 < 1000));
    }
  }
}
// ================= RUTAS DEL SERVIDOR (RESUMIDO) =================

void handleRoot() {
  if (!auth()) return;
  String html = R"rawliteral(

<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><meta charset="utf-8">

<style>

  body{font-family:sans-serif;background:#0f172a;color:white;text-align:center;margin:0;padding:10px;}

  .jornada{background:#1e293b;border-radius:15px;margin:15px 0;padding:10px;border-top:4px solid #38bdf8;}

  .cols{display:flex;gap:10px;margin-top:10px;}

  .col{flex:1;background:#0f172a;padding:10px;border-radius:8px;min-height:40px;}

  .col-t{font-size:0.7em;color:#94a3b8;margin-bottom:5px;font-weight:bold;}

  .time-tag{background:#334155;margin:4px 0;padding:5px;border-radius:4px;font-family:monospace;font-size:1.1em;}

  button{padding:10px;margin:5px;border:none;border-radius:10px;color:white;cursor:pointer;font-weight:bold;}

  .add{background:#22c55e;} .top{background:#38bdf8;} .del{background:#ef4444;}

  #reloj{font-size:2.2em;color:#38bdf8;margin:10px 0;font-weight:bold;}

</style></head>

<body>

<div style="text-align:center;">


 <h3>)rawliteral"
                + nombreEscuela + R"rawliteral(</h3>

</div>

  <div id="reloj">00:00:00</div>

  <button class="top" onclick="location.href='/config'">Configuracion</button>

  <button class="top" style="background:#f59e0b" onclick="fetch('/probar')">Probar Timbre</button>

 

  <div class="jornada">

    <div style="font-weight:bold">MATUTINA</div>

    <div class="cols">

      <div class="col"><div class="col-t">LUNES</div><div id="m-lun"></div></div>

      <div class="col"><div class="col-t">MARTES-VIERNES</div><div id="m-sem"></div></div>

    </div>

  </div>

  <div class="jornada" style="border-top-color:#f59e0b">

    <div style="font-weight:bold">VESPERTINA</div>

    <div class="cols">

      <div class="col"><div class="col-t">LUNES</div><div id="v-lun"></div></div>

      <div class="col"><div class="col-t">MARTES-VIERNES</div><div id="v-sem"></div></div>

    </div>

  </div>

  <div class="jornada" style="border-top-color:#ef4444">

    <div style="font-weight:bold">NOCTURNA</div>

    <div class="col"><div class="col-t">LUNES A VIERNES</div><div id="n-todo"></div></div>

  </div>


  <div class="jornada" style="border-top:none">

    <h4>Añadir Horario</h4>

    <select id="tipoA"><option value="lunes">Lunes</option><option value="semana">Mar a Vier</option><option value="noche">Noche (Todo)</option></select>

    <input type="time" id="horaA"><button class="add" onclick="add()">+</button>

    <br><br><button class="del" onclick="if(confirm('¿Borrar todo?'))location.href='/limpiar'">BORRAR TODO</button>

  </div>

    <div class="jornada" style="border-top:none">

      <h4>Diseñado por:Luis Diaz / 95093267  </h4>

      </div>



<script>

  function upReloj(){ fetch('/getHora').then(r=>r.text()).then(t=>{document.getElementById('reloj').innerText=t;}); }

  setInterval(upReloj,1000);


  // --- CIERRE DE SESION POR INACTIVIDAD ---

  let timeout;

  function resetTimer(){

    clearTimeout(timeout);

    timeout = setTimeout(()=>{ alert("Sesion expirada"); location.href="/logout"; }, 300000); // 5 min

  }

  window.onload=resetTimer; window.onmousemove=resetTimer; window.onkeypress=resetTimer;


  function load(){

    fetch('/horarios').then(r=>r.json()).then(data=>{

      const ids = ["m-lun","m-sem","v-lun","v-sem","n-todo"];

      ids.forEach(id=>document.getElementById(id).innerHTML="");

      data.forEach(i=>{

        const hDec = i.h + (i.m/60);

        const tStr = `<div class="time-tag">${i.h}:${i.m<10?'0':''}${i.m}</div>`;

        if(hDec <= 12.0){ document.getElementById(i.t==='lunes'?'m-lun':'m-sem').innerHTML+=tStr; }

        else if(hDec < 18.0){ document.getElementById(i.t==='lunes'?'v-lun':'v-sem').innerHTML+=tStr; }

        else if(hDec >= 18.0 && hDec <= 21.5){ document.getElementById('n-todo').innerHTML+=tStr; }

      });

    });

  }


  function add(){

    let t=document.getElementById('tipoA').value; let v=document.getElementById('horaA').value;

    if(!v)return; let p=v.split(":");

    fetch('/agregar',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`tipo=${t}&h=${p[0]}&m=${p[1]}`}).then(load);

  }

  load();

</script></body></html>)rawliteral";

  server.send(200, "text/html", html);
}



// ================= SETUP =================

void setup() {
  Serial.begin(115200);
  setenv("TZ", "CST6", 1);
  tzset();
  pinMode(timbrePin, OUTPUT);
  pinMode(ledPin, OUTPUT);
  pinMode(ledPowerPin, OUTPUT);
  pinMode(ledAPPin, OUTPUT);
  pinMode(ledWifiPin, OUTPUT);

  digitalWrite(ledPowerPin, HIGH);  // Encender luz de poder apenas arranque

  Wire.begin(21, 22);
  if (!rtcExternal.begin()) {
    Serial.println("❌ No se encontro el DS3231");
  }

  prefs.begin("timbre", false);
  www_username = prefs.getString("user", "admin");
  www_password = prefs.getString("pass", "9509");
  duracionTimbre = prefs.getInt("duracion", 5);
  
  
  nombreEscuela = prefs.getString("nombre", "TU NOMBRE");

  cargarHorarios();
  conectarWiFi();

  // Handlers
  server.on("/", handleRoot);
  server.on("/getHora", []() {
    server.send(200, "text/plain", rtcInternal.getTime("%H:%M:%S"));
  });
  server.on("/probar", []() {
    activarTimbre();
    server.send(200, "text/plain", "ok");
  });
  server.on("/logout", []() {
    String html = "<html><body style='background:#0f172a;color:white;text-align:center;font-family:sans-serif;padding-top:50px;'>";

    html += "<h1>Sesion cerrada</h1>";

    html += "<button onclick=\"location.href='/'\" style='padding:15px;background:#38bdf8;color:white;border:none;border-radius:10px;cursor:pointer;'>IR AL INICIO / RE-CONECTAR</button>";

    html += "</body></html>";

    server.send(401, "text/html", html);
  });
  server.on("/horarios", []() {
    String j = "[";
    auto addJson = [&](Horario* arr, int total, String tipo) {
      for (int i = 0; i < total; i++) {
        if (j != "[") j += ",";
        j += "{\"h\":" + String(arr[i].hora) + ",\"m\":" + String(arr[i].minuto) + ",\"t\":\"" + tipo + "\"}";
      }
    };
    addJson(hLunes, tLunes, "lunes");
    addJson(hSemana, tSemana, "semana");
    addJson(hNoche, tNoche, "noche");
    j += "]";
    server.send(200, "application/json", j);
  });

  server.on("/agregar", HTTP_POST, []() {
    String t = server.arg("tipo");
    int h = server.arg("h").toInt();
    int m = server.arg("m").toInt();
    if (t == "lunes" && tLunes < 30) {
      hLunes[tLunes].hora = h;
      hLunes[tLunes++].minuto = m;
    } else if (t == "semana" && tSemana < 30) {
      hSemana[tSemana].hora = h;
      hSemana[tSemana++].minuto = m;
    } else if (t == "noche" && tNoche < 30) {
      hNoche[tNoche].hora = h;
      hNoche[tNoche++].minuto = m;
    }
    guardarHorarios(t);
    server.send(200, "ok");
  });

  server.on("/limpiar", []() {
    prefs.remove("lunes");
    prefs.remove("semana");
    prefs.remove("noche");
    tLunes = 0;
    tSemana = 0;
    tNoche = 0;
    server.sendHeader("Location", "/");
    server.send(303);
  });

  server.on("/config", []() {
    if (!auth()) return;

    String html = "<!DOCTYPE html><html><head>";
    html += "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    html += "<style>";
    html += "body{font-family:sans-serif; background:#0f172a; color:white; text-align:center; padding:20px;}";
    html += "form{background:#1e293b; padding:30px; border-radius:15px; display:inline-block; width:100%; max-width:400px; box-sizing:border-box;}";
    html += "input{width:100%; padding:12px; margin:10px 0; border-radius:8px; border:1px solid #334155; background:#0f172a; color:white; font-size:16px; box-sizing:border-box;}";
    html += "label{display:block; text-align:left; color:#94a3b8; font-size:0.9em; margin-top:10px;}";
    html += "button{background:#22c55e; color:white; padding:15px; border:none; border-radius:10px; width:100%; font-weight:bold; font-size:16px; cursor:pointer; margin-top:20px;}";
    html += ".back{background:#38bdf8; margin-top:10px;}";
    html += "</style></head><body>";

    html += "<h2>CONFIGURACION</h2>";
    html += "<form action='/guardarConfig' method='POST'>";
    html += "<label>Nombre del Instituto:</label><input name='nom' value='" + nombreEscuela + "'>";
    html += "<label>Red WiFi (SSID):</label><input name='ssid' value='" + prefs.getString("wifi_ssid") + "'>";
    html += "<label>Contraseña WiFi:</label><input name='wp' type='password' value='" + prefs.getString("wifi_pass") + "'>";
    html += "<hr style='border:0.5px solid #334155; margin:20px 0;'>";
    html += "<label>Usuario Web:</label><input name='usr' value='" + www_username + "'>";
    html += "<label>Password Web:</label><input name='pwd' type='password' value='" + www_password + "'>";
    html += "<label>Duracion del Timbre (seg):</label><input name='dur' type='number' value='" + String(duracionTimbre) + "'>";

    html += "<button type='submit'>GUARDAR Y REINICIAR</button>";
    html += "<button type='button' class='back' onclick='location.href=\"/\"'>CANCELAR</button>";
    html += "</form></body></html>";

    server.send(200, "text/html", html);
  });

  
   server.on("/guardarConfig", HTTP_POST, []() {
  if (!auth()) return;
prefs.begin("config", false); // Abre el almacenamiento
prefs.putString("wifi_ssid", server.arg("ssid"));
  // Guardar configuración
  prefs.putString("wifi_ssid", server.arg("ssid"));
  prefs.putString("wifi_pass", server.arg("wp"));

  prefs.putString("user", server.arg("usr"));
  prefs.putString("pass", server.arg("pwd"));

  prefs.putString("nombre", server.arg("nom"));

  prefs.putInt("duracion", server.arg("dur").toInt());

  prefs.end();

  // Página temporal
  String html = R"rawliteral(
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="4;url=/">
    <title>Configuración Guardada</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --accent: #22c55e;
            --text: #f8fafc;
        }
        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .container {
            background: var(--card);
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 90%;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        h2 { margin: 0 0 0.5rem 0; color: var(--accent); }
        p { opacity: 0.8; margin: 0.5rem 0; }
        
        /* Spinner de carga */
        .loader {
            border: 3px solid rgba(255,255,255,0.1);
            border-top: 3px solid var(--accent);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 1.5rem auto 0;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="icon">✅</span>
        <h2>¡Todo listo!</h2>
        <p>La configuración se guardó con éxito.</p>
        <p><small>Reiniciando sistema...</small></p>
        <div class="loader"></div>
    </div>
</body>
</html>
)rawliteral";

 server.send(200, "text/html", html);

  // En lugar de delay y restart aquí, activamos la bandera
  solicitarReinicio = true;
  tiempoReinicio = millis(); 
});
  server.begin();
  Serial.println("✅ Servidor web iniciado");
}

// ================= LOOP PRINCIPAL =================

void loop() {
  server.handleClient();
  if (solicitarReinicio && (millis() - tiempoReinicio > 2000)) {
    Serial.println("Reiniciando dispositivo...");
    ESP.restart();
  }
  actualizarTimbre();
  actualizarLedsEstado();
  // Reconexión WiFi y Resincronización NTP cada hora
  if (!modoAP && WiFi.status() != WL_CONNECTED) {
    if (millis() - ultimoIntentoWiFi >= intervaloReconexion) {
      WiFi.reconnect();
      ultimoIntentoWiFi = millis();
    }
  }

  // Verificación de Horarios
  int h = rtcInternal.getHour(true);
  int m = rtcInternal.getMinute();
  int d = rtcInternal.getDayofWeek();  // 1: Lun, 2: Mar...

  if (m != ultimoMinuto) {
    bool sonar = false;
    if (d == 1) {  // Lunes
      for (int i = 0; i < tLunes; i++)
        if (h == hLunes[i].hora && m == hLunes[i].minuto) sonar = true;
    }
    if (d >= 2 && d <= 5) {  // Martes a Viernes
      for (int i = 0; i < tSemana; i++)
        if (h == hSemana[i].hora && m == hSemana[i].minuto) sonar = true;
    }
    if (d >= 1 && d <= 5) {  // Lunes a Viernes (Noche/Todo)
      for (int i = 0; i < tNoche; i++)
        if (h == hNoche[i].hora && m == hNoche[i].minuto) sonar = true;
    }

    if (sonar) activarTimbre();
    ultimoMinuto = m;

    // Cada media noche (o cada hora), podrías forzar una sincronización con el RTC físico
    // para evitar derivas del reloj interno del ESP32.
    if (m == 0) {
      DateTime now = rtcExternal.now();
      rtcInternal.setTime(now.second(), now.minute(), now.hour(), now.day(), now.month(), now.year());
    }
  }
}