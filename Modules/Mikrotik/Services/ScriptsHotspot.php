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
                :local f [/system clock get date];
                :local t [/system clock get time];
                :local z [:tostr [/system clock get gmt-offset]];
                :if ($f != [/system clock get date]) do={ :set f [/system clock get date]; :set t [/system clock get time]; };
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
                :local sign 1;
                :if ([:pick $z 0 1] = "-") do={ :set sign -1; :set z [:pick $z 1 [:len $z]]; } else={
                    :if ([:pick $z 0 1] = "+") do={ :set z [:pick $z 1 [:len $z]]; };
                };
                :if (([:len $z] != 5) && ([:len $z] != 8)) do={ :return 0; };
                :if ([:pick $z 2 3] != ":") do={ :return 0; };
                :if (([:len $z] = 8) && ([:pick $z 5 8] != ":00")) do={ :return 0; };
                :local zh [:tonum [:pick $z 0 2]]; :local zm [:tonum [:pick $z 3 5]];
                :if (([:typeof $h] != "num") || ([:typeof $n] != "num") || ([:typeof $s] != "num") || ([:typeof $zh] != "num") || ([:typeof $zm] != "num")) do={ :return 0; };
                :if (($h < 0) || ($h > 23) || ($n < 0) || ($n > 59) || ($s < 0) || ($s > 59) || ($zh < 0) || ($zh > 14) || ($zm < 0) || ($zm > 59)) do={ :return 0; };
                :return (($days * 86400) + ($h * 3600) + ($n * 60) + $s - ($sign * (($zh * 3600) + ($zm * 60))));
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

    public static function login(Router $r): string
    {
        return strtr(<<<'ROS'
            :local uid [/ip hotspot user find where name=$user];
            :if ([:len $uid] = 0) do={ :error "Ficha inexistente"; };
            :local comentario [/ip hotspot user get $uid comment];
            :local vence 0;
            :if ([:pick $comentario 0 @LONG@] = "@PREFIJO@:") do={
                :set vence [:tonum [:pick $comentario @LONG@ [:len $comentario]]];
            };
            @RELOJ@
            :local caducada true;
            :if ([:typeof $vence] = "num") do={
                :set caducada (($ahora < 1735689600) || ($ahora >= $vence));
            };
            :if ($caducada) do={
                @BLOQUEAR@
                :error "Ficha vencida o reloj invalido";
            };
        ROS, ['@PREFIJO@' => $r->marcador(), '@LONG@' => strlen($r->marcador()) + 1, '@BLOQUEAR@' => self::bloquear(), '@RELOJ@' => self::reloj()]);
    }

    public static function barrido(Router $r): string
    {
        return strtr(<<<'ROS'
            @RELOJ@
            :foreach uid in=[/ip hotspot user find where comment~"^@PREFIJO@:"] do={
                :local comentario [/ip hotspot user get $uid comment];
                :local vence [:tonum [:pick $comentario @LONG@ [:len $comentario]]];
                :local caducada true;
                :if ([:typeof $vence] = "num") do={
                    :set caducada (($ahora < 1735689600) || ($ahora >= $vence));
                };
                :if ($caducada) do={
                    @BLOQUEAR@
                };
            };
            /system script set [/system script find where name="@TAREA@"] comment=("ok:" . $ahora);
        ROS, ['@PREFIJO@' => $r->marcador(), '@LONG@' => strlen($r->marcador()) + 1,
            '@TAREA@' => $r->tarea(), '@BLOQUEAR@' => self::bloquear(), '@RELOJ@' => self::reloj()]);
    }
}
