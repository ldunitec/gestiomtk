<?php
$ip = '192.168.10.32';  // Cambia esto por la IP real de tu VF30
$port = 5010;           // Puerto estándar, cambia si tu dispositivo usa otro
$device_id= 1;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "Error creando socket: " . socket_strerror(socket_last_error()) . "\n";
    exit;
}

echo "Conectando a $ip:$port...\n";
$result = socket_connect($socket, $ip, $port);
if ($result === false) {
    echo "No se pudo conectar: " . socket_strerror(socket_last_error($socket)) . "\n";
    exit;
} else {
    echo "¡Conectado!\n";
}

// Intentamos leer alguna respuesta automática
$response = socket_read($socket, 2048);
if ($response === false) {
    echo "No se recibió respuesta o hubo un error.\n";
} else {
    echo "Respuesta recibida (hex): " . bin2hex($response) . "\n";
}

function calcularChecksum($data) {
    $sum = 0;
    for ($i = 0; $i < strlen($data); $i++) {
        $sum += ord($data[$i]);
    }
    return chr($sum & 0xFF);
}

// Construir paquete de comando binario
$header = chr(0xA5); // Header
$command = chr(0x20); // Comando: descargar registros
$devId = chr($device_id); // ID del dispositivo
$data = ""; // No se necesita payload en este comando
$length = chr(strlen($data)); // Tamaño de data
$payload = $command . $devId . $length . $data;
$checksum = calcularChecksum($payload);
$packet = $header . $payload . $checksum;

echo "Enviando paquete: " . bin2hex($packet) . "\n";

// Crear socket y conectar
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("Error al crear socket: " . socket_strerror(socket_last_error()));
}
if (!socket_connect($socket, $ip, $port)) {
    die("No se pudo conectar: " . socket_strerror(socket_last_error($socket)));
}

// Enviar el paquete
socket_write($socket, $packet, strlen($packet));

// Leer respuesta (puede tardar)
$response = socket_read($socket, 4096);
if ($response === false) {
    echo "No se recibió respuesta\n";
} else {
    echo "Respuesta (hex): " . bin2hex($response) . "\n";
}






socket_close($socket);

