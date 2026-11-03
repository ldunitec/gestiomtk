<?php

namespace Modules\Mikrotik\Services;

use Modules\Mikrotik\Entities\Router;

class ScriptsHotspot
{
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
            :local ahora ([:tonsec [:timestamp]] / 1000000000);
            :local caducada true;
            :if ([:typeof $vence] = "num") do={
                :set caducada (($ahora < 1735689600) || ($ahora >= $vence));
            };
            :if ($caducada) do={
                @BLOQUEAR@
                :error "Ficha vencida o reloj invalido";
            };
        ROS, ['@PREFIJO@' => $r->marcador(), '@LONG@' => strlen($r->marcador()) + 1, '@BLOQUEAR@' => self::bloquear()]);
    }

    public static function barrido(Router $r): string
    {
        return strtr(<<<'ROS'
            :local ahora ([:tonsec [:timestamp]] / 1000000000);
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
            '@TAREA@' => $r->tarea(), '@BLOQUEAR@' => self::bloquear()]);
    }
}
