<?php
class Anviz
{
    const ACK_SUCCESS = '00';
    const ACK_FAIL = '01';
    const getDateTime = '38';
    const setDateTime = '39';
    const getInfo = '30';
    const setInfo = '31';
    const getLogs = '40';

    protected $crc_table = [0x0000, 0x1189, 0x2312, 0x329B, 0x4624, 0x57AD, 0x6536, 0x74BF,
    0x8C48, 0x9DC1, 0xAF5A, 0xBED3, 0xCA6C, 0xDBE5, 0xE97E, 0xF8F7, 0x1081, 0x0108, 0x3393, 0x221A,
    0x56A5, 0x472C, 0x75B7, 0x643E, 0x9CC9, 0x8D40, 0xBFDB, 0xAE52, 0xDAED, 0xCB64, 0xF9FF, 0xE876];

    protected $socket = null;
    public $mode = 'client';
    public $id;
    public $port;
    public $ip;

    public function __construct($mode, $id, $port, $ip)
    {
        $this->mode = $mode;
        $this->id = $id;
        $this->port = $port;
        $this->ip = $ip;
        $this->initSocket();
    }

    private function initSocket()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$this->socket) {
            throw new AnvizException("Error creando socket: " . socket_strerror(socket_last_error()));
        }
        if (!socket_connect($this->socket, $this->ip, $this->port)) {
            throw new AnvizException("No se pudo conectar al dispositivo: " . socket_strerror(socket_last_error($this->socket)));
        }
    }

    private function padHex($hex, $length)
    {
        while (strlen($hex) < $length) {
            $hex = '0' . $hex;
        }
        return $hex;
    }

    private function tc_crc16($b)
    {
        $crc = 0xFFFF;
        for ($l = 0; $l < strlen($b); $l++) {
            $crc ^= ord($b[$l]);
            $crc = ($crc >> 8) ^ $this->crc_table[$crc & 255];
        }
        $res = strtoupper(dechex($crc));
        $res = $this->padHex($res, 4);
        return $res[2] . $res[3] . $res[0] . $res[1];
    }

    private function requestBuilder($command, $data = '')
    {
        $request = 'A5';
        $request .= $this->padHex(dechex($this->id), 8);
        $request .= $this->padHex($command, 2);
        $request .= $this->padHex(dechex(strlen($data) / 2), 4);
        $request .= $data;
        $request .= $this->tc_crc16(hex2bin($request));
        return hex2bin($request);
    }

    public function readAttendanceRecords()
    {
        $request = $this->requestBuilder(self::getLogs);
        socket_write($this->socket, $request);
        $response = socket_read($this->socket, 4096);
        if ($response === false) {
            throw new AnvizException("Error leyendo respuesta.");
        }
        $hex = bin2hex($response);
        $bytes = str_split($hex, 2);
        if (!isset($bytes[7])) {
            throw new AnvizException("Respuesta incompleta.");
        }
        if ($bytes[7] === self::ACK_SUCCESS) {
            $length = hexdec($bytes[8] . $bytes[9]);
            $data = array_slice($bytes, 10, $length * 2);
            return $data;
        } elseif ($bytes[7] === self::ACK_FAIL) {
            return [];
        } else {
            throw new AnvizException("Respuesta inválida del dispositivo.");
        }
    }

    public function parseAttendanceData(array $dataBytes)
    {
        $records = [];
        $recordSize = 16;
        $total = count($dataBytes) / $recordSize;
        for ($i = 0; $i < $total; $i++) {
            $offset = $i * $recordSize;
            $idHex = implode(array_reverse(array_slice($dataBytes, $offset, 4)));
            $employeeId = hexdec($idHex);
            $timestampHex = implode(array_reverse(array_slice($dataBytes, $offset + 4, 4)));
            $timestamp = hexdec($timestampHex);
            $datetime = date('Y-m-d H:i:s', $timestamp);
            $event = hexdec($dataBytes[$offset + 8]);
            $status = hexdec($dataBytes[$offset + 9]);
            $records[] = [
                'employee_id' => $employeeId,
                'datetime' => $datetime,
                'event' => $event,
                'status' => $status
            ];
        }
        return $records;
    }
}

class AnvizException extends Exception {}
