<?php

namespace Modules\Mikrotik\Services;

use Carbon\CarbonImmutable;
use Modules\Mikrotik\Entities\{Router, Ficha};

class HotspotService
{
    public function datos(Conexion $api, Router $r): array
    {
        $resource = $api->ejecutar('/system/resource/print', ['.proplist' => 'version,board-name,uptime,cpu-load,free-memory,total-memory'])[0] ?? [];
        $identity = $api->ejecutar('/system/identity/print', ['.proplist' => 'name'])[0]['name'] ?? '';
        $servers = $api->ejecutar('/ip/hotspot/print', ['.proplist' => '.id,name,disabled,invalid,profile,interface'], ['name' => $r->servidor_hotspot]);
        $interfaces = $api->ejecutar('/interface/print', ['.proplist' => 'name,type,running,disabled']);
        return compact('resource', 'identity', 'servers', 'interfaces');
    }

    private function comprobarEquipo(Conexion $api, Router $r): string
    {
        $resource = $api->ejecutar('/system/resource/print', ['.proplist' => 'version'])[0] ?? [];
        $version = $resource['version'] ?? '';
        if (!preg_match('/^(\d+)\.(\d+)(?:\.(\d+))?/', $version, $m) || version_compare($m[1].'.'.$m[2].'.'.($m[3] ?? 0), '7.16.0', '<')) {
            throw new ErrorRouter('La vigencia por calendario requiere RouterOS 7.16 o posterior. No se modificó la versión del equipo.');
        }
        $server = $api->ejecutar('/ip/hotspot/print', ['.proplist' => '.id,name,disabled,invalid'], ['name' => $r->servidor_hotspot])[0] ?? null;
        if (!$server || $this->esSi($server['disabled'] ?? 'no') || $this->esSi($server['invalid'] ?? 'no')) {
            throw new ErrorRouter('El servidor Hotspot indicado no existe, está deshabilitado o no es válido.');
        }
        return $version;
    }

    public function preparar(Conexion $api, Router $r): void
    {
        $version = $this->comprobarEquipo($api, $r);
        // Objetos exclusivos de este módulo. No se cambia DHCP, NAT, VLAN ni perfiles ajenos.
        $this->guardar($api, '/system/script', $r->tarea(), [
            'source' => ScriptsHotspot::barrido($r), 'policy' => 'read,write,policy,test', 'comment' => '']);
        $this->guardar($api, '/ip/hotspot/user/profile', $r->perfil(), [
            'shared-users' => '1', 'add-mac-cookie' => 'no', 'rate-limit' => $r->velocidad,
            'on-login' => ScriptsHotspot::login($r)]);
        $this->guardar($api, '/system/scheduler', $r->tarea(), [
            'on-event' => $r->tarea(), 'interval' => '30s', 'start-time' => '00:00:00',
            'policy' => 'read,write,policy,test', 'disabled' => 'no']);
        $this->comprobarProteccion($api, $r);
        $r->update(['preparado_at' => CarbonImmutable::now('UTC'), 'verificado_at' => CarbonImmutable::now('UTC'), 'version' => $version]);
    }

    private function guardar(Conexion $api, string $ruta, string $nombre, array $campos): void
    {
        $actual = $api->ejecutar($ruta.'/print', ['.proplist' => '.id,name'], ['name' => $nombre])[0] ?? null;
        $api->ejecutar($ruta.($actual ? '/set' : '/add'), array_merge($campos,
            $actual ? ['.id' => $actual['.id']] : ['name' => $nombre]));
    }

    private function esSi($value): bool
    {
        return in_array($value, [true, 'true', 'yes'], true);
    }

    public function listo(Conexion $api, Router $r): void
    {
        if (!$r->preparado_at) { throw new ErrorRouter('Primero prepare el Hotspot desde la ficha del router.'); }
        $this->comprobarEquipo($api, $r);
        $this->comprobarProteccion($api, $r);
    }

    private function normalizar(string $s): string
    {
        return trim(str_replace("\r\n", "\n", $s));
    }

    private function comprobarProteccion(Conexion $api, Router $r): void
    {
        $script = $api->ejecutar('/system/script/print', ['.proplist' => '.id,name,source,comment'], ['name' => $r->tarea()])[0] ?? [];
        $scheduler = $api->ejecutar('/system/scheduler/print', ['.proplist' => '.id,name,on-event,interval,disabled,policy'], ['name' => $r->tarea()])[0] ?? [];
        $perfil = $api->ejecutar('/ip/hotspot/user/profile/print', ['.proplist' => 'name,on-login,shared-users,add-mac-cookie'], ['name' => $r->perfil()])[0] ?? [];
        if ($this->normalizar($script['source'] ?? '') !== $this->normalizar(ScriptsHotspot::barrido($r))
            || ($scheduler['on-event'] ?? '') !== $r->tarea() || $this->esSi($scheduler['disabled'] ?? true)
            || !in_array($scheduler['interval'] ?? '', ['30s', '00:00:30'], true)
            || count(array_diff(['read','write','policy','test'], explode(',', $scheduler['policy'] ?? ''))) > 0
            || $this->normalizar($perfil['on-login'] ?? '') !== $this->normalizar(ScriptsHotspot::login($r))
            || (string) ($perfil['shared-users'] ?? '') !== '1' || $this->esSi($perfil['add-mac-cookie'] ?? true)) {
            throw new ErrorRouter('La protección de vigencia está incompleta o cambió. Prepare nuevamente el Hotspot antes de emitir fichas.');
        }
        $api->ejecutar('/system/script/run', ['.id' => $script['.id']]);
        $resultado = $api->ejecutar('/system/script/print', ['.proplist' => 'comment'], ['name' => $r->tarea()])[0]['comment'] ?? '';
        if (!preg_match('/^ok:(\d{10})$/D', $resultado, $m) || abs((int) $m[1] - CarbonImmutable::now('UTC')->timestamp) > 90) {
            throw new ErrorRouter('El script no confirmó una hora válida. Revise reloj/NTP y permisos de scripts en RouterOS.');
        }
    }

    private function usuario(Conexion $api, Ficha $f): ?array
    {
        return $api->ejecutar('/ip/hotspot/user/print', ['.proplist' => '.id,name,comment,profile,server,disabled'], ['name' => $f->usuario])[0] ?? null;
    }

    private function comentario(Ficha $f): string
    {
        return $f->router->marcador().':'.$f->vence_at->timestamp;
    }

    private function verificarPropiedad(array $u, Ficha $f): void
    {
        if (($u['comment'] ?? '') !== $this->comentario($f) || ($u['profile'] ?? '') !== $f->router->perfil()
            || ($u['server'] ?? '') !== $f->router->servidor_hotspot) {
            throw new ErrorRouter('Existe un usuario con el mismo nombre que no corresponde a esta ficha. No se modificó.');
        }
    }

    public function publicar(Conexion $api, Ficha $f): void
    {
        $u = $this->usuario($api, $f);
        if ($u) { $this->verificarPropiedad($u, $f); }
        $datos = ['password' => $f->clave, 'profile' => $f->router->perfil(), 'server' => $f->router->servidor_hotspot,
            'comment' => $this->comentario($f), 'disabled' => 'yes'];
        $api->ejecutar('/ip/hotspot/user/'.($u ? 'set' : 'add'), array_merge($datos, $u ? ['.id' => $u['.id']] : ['name' => $f->usuario]));
        $u = $this->usuario($api, $f);
        if (!$u) { throw new ErrorRouter('No se confirmó la creación en el router. Reintente la misma ficha.'); }
        $this->verificarPropiedad($u, $f);
        if ($f->vence_at->isPast() || $f->revocada_at) { throw new ErrorRouter('La ficha ya venció o fue revocada.'); }
        $api->ejecutar('/ip/hotspot/user/set', ['.id' => $u['.id'], 'disabled' => 'no']);
        $verificado = $this->usuario($api, $f);
        if (!$verificado || $this->esSi($verificado['disabled'] ?? true)) {
            throw new ErrorRouter('El router no confirmó la habilitación de la ficha.');
        }
        $this->verificarPropiedad($verificado, $f);
    }

    public function desconectar(Conexion $api, Ficha $f): void
    {
        $u = $this->usuario($api, $f);
        if ($u) { $this->verificarPropiedad($u, $f); }
        foreach ($api->ejecutar('/ip/hotspot/active/print', ['.proplist' => '.id,user,server'], ['user' => $f->usuario]) as $s) {
            if (($s['server'] ?? '') === $f->router->servidor_hotspot) {
                $api->ejecutar('/ip/hotspot/active/remove', ['.id' => $s['.id']]);
            }
        }
        foreach ($api->ejecutar('/ip/hotspot/cookie/print', ['.proplist' => '.id,user'], ['user' => $f->usuario]) as $cookie) {
            $api->ejecutar('/ip/hotspot/cookie/remove', ['.id' => $cookie['.id']]);
        }
    }

    public function bloquear(Conexion $api, Ficha $f): void
    {
        $u = $this->usuario($api, $f);
        if ($u) {
            $this->verificarPropiedad($u, $f);
            $api->ejecutar('/ip/hotspot/user/set', ['.id' => $u['.id'], 'disabled' => 'yes']);
        }
        $this->desconectar($api, $f);
        $confirmado = $this->usuario($api, $f);
        if ($confirmado && !$this->esSi($confirmado['disabled'] ?? false)) {
            throw new ErrorRouter('No se confirmó el bloqueo. La ficha seguirá pendiente de conciliación.');
        }
    }

    public function sesiones(Conexion $api, Router $r): array
    {
        return $api->ejecutar('/ip/hotspot/active/print', ['.proplist' => 'user,address,mac-address,uptime,bytes-in,bytes-out,server'], ['server' => $r->servidor_hotspot]);
    }
}
