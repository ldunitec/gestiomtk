<?php

namespace Modules\Mikrotik\Services;

use Modules\Mikrotik\Entities\Router;

class ScriptsHotspot
{
    private static function reloj(): string
    {
        // RouterOS 6 no tiene :timestamp/:tonsec. Convertir su reloj local a UTC
        // mantiene el mismo formato de vencimiento de las fichas ya emitidas.
        return <<<'ROS'
            :local gmtkUtc do={
                :local f [:tostr [/system clock get date]];
                :local t [:tostr [/system clock get time]];
                :if ($f != [:tostr [/system clock get date]]) do={ :set f [:tostr [/system clock get date]]; :set t [:tostr [/system clock get time]]; };
                :local y 0; :local m 0; :local d 0;
                :if (([:len $f] = 11) && ([:pick $f 3 4] = "/")) do={
                    :local p [:find "janfebmaraprmayjunjulaugsepoctnovdec" [:pick $f 0 3]];
                    :if ([:typeof $p] != "num") do={ :return 0; };
                    :if (($p % 3) != 0) do={ :return 0; };
                    :set y [:tonum [:pick $f 7 11]]; :set m (($p / 3) + 1); :set d [:tonum [:pick $f 4 6]];
                } else={
                    :if (([:len $f] != 10) || ([:pick $f 4 5] != "-") || ([:pick $f 7 8] != "-")) do={ :return 0; };
                    :set y [:tonum [:pick $f 0 4]]; :set m [:tonum [:pick $f 5 7]]; :set d [:tonum [:pick $f 8 10]];
                };
                :if (([:typeof $y] != "num") || ([:typeof $m] != "num") || ([:typeof $d] != "num")) do={ :return 0; };
                :if (($y < 1970) || ($y > 2099) || ($m < 1) || ($m > 12) || ($d < 1)) do={ :return 0; };
                :local leap ((($y % 4) = 0) && ((($y % 100) != 0) || (($y % 400) = 0)));
                :local lengths {31;28;31;30;31;30;31;31;30;31;30;31};
                :local idx ($m - 1); :local maxday ($lengths->$idx);
                :if (($m = 2) && $leap) do={ :set maxday 29; };
                :if ($d > $maxday) do={ :return 0; };
                :local sums {0;31;59;90;120;151;181;212;243;273;304;334};
                :local prev ($y - 1);
                :local days ((365 * ($y - 1970)) + ($prev / 4) - ($prev / 100) + ($prev / 400) - 477 + ($sums->$idx) + $d - 1);
                :if (($m > 2) && $leap) do={ :set days ($days + 1); };
                :if (([:len $t] != 8) || ([:pick $t 2 3] != ":") || ([:pick $t 5 6] != ":")) do={ :return 0; };
                :local h [:tonum [:pick $t 0 2]]; :local n [:tonum [:pick $t 3 5]]; :local s [:tonum [:pick $t 6 8]];
                :if (([:typeof $h] != "num") || ([:typeof $n] != "num") || ([:typeof $s] != "num")) do={ :return 0; };
                :if (($h < 0) || ($h > 23) || ($n < 0) || ($n > 59) || ($s < 0) || ($s > 59)) do={ :return 0; };
                :local localSeconds (($days * 86400) + ($h * 3600) + ($n * 60) + $s);
                :local offset [:tonum [/system clock get gmt-offset]];
                :if ([:typeof $offset] != "num") do={ :return 0; };
                :if (($offset >= 2147483648) && ($offset <= 4294967295)) do={ :set offset ($offset - 4294967296); };
                :if (($offset < -50400) || ($offset > 50400)) do={ :return 0; };
                :return ($localSeconds - $offset);
            };
            :local ahora [$gmtkUtc];
        ROS;
    }

    private static function bloquear(): string
    {
        return <<<'ROS'
            /ip hotspot user set $uid disabled=yes;
            :local nombre [/ip hotspot user get $uid name];
            /ip hotspot active remove [/ip hotspot active find where user=$nombre];
            /ip hotspot cookie remove [/ip hotspot cookie find where user=$nombre];
        ROS;
    }

    private static function leerVigencia(): string
    {
        return <<<'ROS'
            :local valor [:pick $comentario @LONG@ [:len $comentario]];
            :local vence 0;
            :if ($valor = "E") do={ :set vence -1; };
            :if (($valor = "P1") || ($valor = "P2") || ($valor = "P7")) do={ :set vence -1; };
            :if ([:len $valor] = 10) do={ :set vence [:tonum $valor]; };
            :if (([:len $valor] = 24) && ([:pick $valor 0 1] = "A") && ([:pick $valor 2 3] = ":") && ([:pick $valor 13 14] = ":")) do={
                :local dias [:tonum [:pick $valor 1 2]];
                :local inicio [:tonum [:pick $valor 3 13]];
                :local fin [:tonum [:pick $valor 14 24]];
                :if (([:typeof $dias] = "num") && ([:typeof $inicio] = "num") && ([:typeof $fin] = "num")) do={
                    :if ((($dias = 1) || ($dias = 2) || ($dias = 7)) && ($inicio >= 1735689600) && (($fin - $inicio) = ($dias * 86400))) do={ :set vence $fin; };
                };
            };
            :local caducada true;
            :if ([:typeof $vence] = "num") do={
                :if ($vence = -1) do={ :set caducada false; } else={ :set caducada (($ahora < 1735689600) || ($ahora >= $vence)); };
            };
        ROS;
    }

    public static function login(Router $r): string
    {
        return self::render(<<<'ROS'
            :local uid [/ip hotspot user find where name=$user];
            :if ([:len $uid] = 0) do={ :error "Ficha inexistente"; };
            :local comentario [/ip hotspot user get $uid comment];
            :if ([:pick $comentario 0 @LONG@] != "@PREFIJO@:") do={ :error "Ficha ajena"; };
            @RELOJ@
            :local pendiente [:pick $comentario @LONG@ [:len $comentario]];
            :if (($pendiente = "P1") || ($pendiente = "P2") || ($pendiente = "P7")) do={
                :if ($ahora < 1735689600) do={
                    /ip hotspot active remove [/ip hotspot active find where user=$user];
                    :error "Reloj invalido: ficha sin activar";
                };
                :local dias [:tonum [:pick $pendiente 1 2]];
                :local fin ($ahora + ($dias * 86400));
                :if ([/ip hotspot user get $uid comment] = $comentario) do={
                    /ip hotspot user set $uid comment=("@PREFIJO@:A" . $dias . ":" . $ahora . ":" . $fin);
                };
                :set comentario [/ip hotspot user get $uid comment];
            };
            @VIGENCIA@
            :if ($caducada) do={
                @BLOQUEAR@
                :error "Ficha vencida o vigencia invalida";
            };
        ROS, $r);
    }

    public static function barrido(Router $r): string
    {
        return self::render(<<<'ROS'
            @RELOJ@
            :foreach uid in=[/ip hotspot user find where comment~"^@PREFIJO@:"] do={
                :local comentario [/ip hotspot user get $uid comment];
                @VIGENCIA@
                :if ($caducada) do={
                    @BLOQUEAR@
                };
            };
            /system script set [/system script find where name="@TAREA@"] comment=("ok:" . $ahora);
        ROS, $r);
    }

    private static function render(string $source, Router $r): string
    {
        $source = strtr($source, ['@VIGENCIA@' => self::leerVigencia(), '@BLOQUEAR@' => self::bloquear(), '@RELOJ@' => self::reloj()]);
        return strtr($source, ['@PREFIJO@' => $r->marcador(), '@LONG@' => strlen($r->marcador()) + 1, '@TAREA@' => $r->tarea()]);
    }
}
