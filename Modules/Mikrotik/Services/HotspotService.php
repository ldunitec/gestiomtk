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
        if (!preg_match('/^(\d+)\.(\d+)(?:\.(\d+))?/', $version, $m)
            || !(((int) $m[1] === 6 && (int) $m[2] >= 49) || (int) $m[1] === 7)) {
            throw new ErrorRouter('El módulo admite RouterOS 6.49.x y 7.x. La protección y la hora deben verificarse antes de emitir fichas.');
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
        $this->guardar($api, '/ip/hotspot/user/profile', $r->perfilEmpleados(), [
            'shared-users' => '1', 'add-mac-cookie' => 'no', 'rate-limit' => '5M/5M',
            'on-login' => ScriptsHotspot::login($r)]);
        $this->guardar($api, '/system/scheduler', $r->tarea(), [
            'on-event' => $r->tarea(), 'interval' => '30s', 'start-time' => '00:00:00',
            'policy' => 'read,write,policy,test', 'disabled' => 'no']);
        $this->comprobarProteccion($api, $r);
        $this->migrarEmpleados($api, $r);
        $r->update(['preparado_at' => CarbonImmutable::now('UTC'), 'verificado_at' => CarbonImmutable::now('UTC'), 'version' => $version]);
    }

    private function migrarEmpleados(Conexion $api, Router $r): void
    {
        // Solo usuarios respaldados por una ficha permanente del módulo.
        $r->fichas()->withTrashed()->where('inicio_vigencia', 'permanente')->chunkById(100, function ($fichas) use ($api, $r) {
            foreach ($fichas as $f) {
                $f->setRelation('router', $r);
                $u = $this->usuario($api, $f);
                if (!$u) { continue; }
                $this->verificarPropiedad($u, $f);
                if (($u['profile'] ?? '') === $r->perfilEmpleados()) { continue; }
                $api->ejecutar('/ip/hotspot/user/set', ['.id'=>$u['.id'], 'profile'=>$r->perfilEmpleados()]);
                $nuevo = $this->usuario($api, $f);
                if (!$nuevo || ($nuevo['profile'] ?? '') !== $r->perfilEmpleados()
                    || ($nuevo['disabled'] ?? '') !== ($u['disabled'] ?? '') || ($nuevo['comment'] ?? '') !== ($u['comment'] ?? '')) {
                    throw new ErrorRouter('No se confirmó el cambio al perfil de empleados. Reintente la preparación.');
                }
                $this->verificarPropiedad($nuevo, $f);
                // Las sesiones activas conservan su límite hasta el próximo ingreso.
                $this->desconectar($api, $f);
            }
        });
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
        if ($this->normalizar($script['source'] ?? '') !== $this->normalizar(ScriptsHotspot::barrido($r))
            || ($scheduler['on-event'] ?? '') !== $r->tarea() || $this->esSi($scheduler['disabled'] ?? true)
            || !in_array($scheduler['interval'] ?? '', ['30s', '00:00:30'], true)
            || count(array_diff(['read','write','policy','test'], explode(',', $scheduler['policy'] ?? ''))) > 0) {
            throw new ErrorRouter('La protección de vigencia está incompleta o cambió. Prepare nuevamente el Hotspot antes de emitir fichas.');
        }
        foreach ([$r->perfil() => $r->velocidad, $r->perfilEmpleados() => '5M/5M'] as $nombre => $velocidad) {
            $perfil = $api->ejecutar('/ip/hotspot/user/profile/print', ['.proplist' => 'name,on-login,shared-users,add-mac-cookie,rate-limit'], ['name' => $nombre])[0] ?? [];
            if ($this->normalizar($perfil['on-login'] ?? '') !== $this->normalizar(ScriptsHotspot::login($r))
                || (string) ($perfil['shared-users'] ?? '') !== '1' || $this->esSi($perfil['add-mac-cookie'] ?? true)
                || ($perfil['rate-limit'] ?? '') !== $velocidad) {
                throw new ErrorRouter('El perfil '.$nombre.' está incompleto o cambió. Prepare nuevamente el Hotspot.');
            }
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
        return VigenciaFicha::inicial($f);
    }

    private function verificarPropiedad(array $u, Ficha $f): void
    {
        $perfiles = [$f->router->perfilFicha($f)];
        // Las fichas permanentes emitidas antes de separar perfiles se reconocen por E.
        if ($f->inicio_vigencia === 'permanente') { $perfiles[] = $f->router->perfil(); }
        if (!in_array($u['profile'] ?? '', $perfiles, true)
            || ($u['server'] ?? '') !== $f->router->servidor_hotspot) {
            throw new ErrorRouter('Existe un usuario con el mismo nombre que no corresponde a esta ficha. No se modificó.');
        }
        VigenciaFicha::registrar($f, $u['comment'] ?? '');
    }

    public function publicar(Conexion $api, Ficha $f): void
    {
        $u = $this->usuario($api, $f);
        if ($u) { $this->verificarPropiedad($u, $f); }
        elseif ($f->activada_at || $f->sincronizada_at || $f->estado === 'activa') {
            throw new ErrorRouter('El usuario publicado desapareció del router. No se recreó para evitar reiniciar su vigencia.');
        }
        $datos = ['password' => $f->clave, 'profile' => $f->router->perfilFicha($f), 'server' => $f->router->servidor_hotspot,
            'disabled' => 'yes'];
        if (!$u) { $datos['comment'] = $this->comentario($f); }
        $api->ejecutar('/ip/hotspot/user/'.($u ? 'set' : 'add'), array_merge($datos, $u ? ['.id' => $u['.id']] : ['name' => $f->usuario]));
        $u = $this->usuario($api, $f);
        if (!$u) { throw new ErrorRouter('No se confirmó la creación en el router. Reintente la misma ficha.'); }
        $this->verificarPropiedad($u, $f);
        if ($f->vencida() || $f->revocada_at) { throw new ErrorRouter('La ficha ya venció o fue revocada.'); }
        // Conservar la confirmación previa incluso si se pierde la respuesta al habilitar.
        $f->update(['sincronizada_at' => CarbonImmutable::now('UTC')]);
        $api->ejecutar('/ip/hotspot/user/set', ['.id' => $u['.id'], 'disabled' => 'no']);
        $verificado = $this->usuario($api, $f);
        if (!$verificado || $this->esSi($verificado['disabled'] ?? true)) {
            throw new ErrorRouter('El router no confirmó la habilitación de la ficha.');
        }
        $this->verificarPropiedad($verificado, $f);
    }

    public function sincronizar(Conexion $api, Ficha $f): void
    {
        $u = $this->usuario($api, $f);
        if (!$u) { throw new ErrorRouter('No se encontró la ficha en el router para sincronizar su primer ingreso.'); }
        $this->verificarPropiedad($u, $f);
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


