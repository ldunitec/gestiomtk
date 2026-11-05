<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\{Carbon, CarbonImmutable};
use Illuminate\Support\Facades\{DB, Gate};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Mikrotik\Database\Seeders\MikrotikDatabaseSeeder;
use Modules\Mikrotik\Entities\{Router, Lote, Ficha};
use Modules\Mikrotik\Services\{Conexion, RouterFactory, ErrorRouter, RespuestaApi, HotspotService, FichasService, ScriptsHotspot};
use Spatie\Permission\Models\{Permission, Role};
use Tests\TestCase;

class RouterSimulado implements Conexion
{
    public array $tablas = [];
    public array $escrituras = [];
    public bool $offline = false;
    public bool $perderRespuesta = false;
    public int $desfase = 0;
    private int $secuencia = 20;

    public function __construct()
    {
        $this->tablas = [
            '/system/resource' => [['.id'=>'*1','version'=>'7.16.2 (stable)','board-name'=>'RB1100x4','uptime'=>'1d','cpu-load'=>'4']],
            '/system/identity' => [['.id'=>'*1','name'=>'Router pruebas']],
            '/ip/hotspot' => [['.id'=>'*1','name'=>'visitantes','disabled'=>'false','invalid'=>'false','profile'=>'hsprof1']],
            '/interface' => [['.id'=>'*1','name'=>'bridge-visitas','running'=>'true','disabled'=>'false','type'=>'bridge']],
        ];
    }

    public function ejecutar(string $ruta, array $atributos = [], array $filtros = []): array
    {
        if ($this->offline) { throw new ErrorRouter('Router sin conexión.'); }
        $partes = explode('/', $ruta);
        $accion = array_pop($partes);
        $base = implode('/', $partes);
        if ($accion === 'print') {
            $filas = array_values(array_filter($this->tablas[$base] ?? [], function ($fila) use ($filtros) {
                foreach ($filtros as $k=>$v) { if (($fila[$k] ?? null) !== $v) { return false; } }
                return true;
            }));
            if (isset($atributos['.proplist'])) {
                $claves = array_flip(explode(',', $atributos['.proplist']));
                $filas = array_map(fn ($f) => array_intersect_key($f, $claves), $filas);
            }
            return $filas;
        }
        $this->escrituras[] = [$ruta, $atributos];
        if ($accion === 'run') {
            foreach ($this->tablas['/system/script'] as &$fila) {
                if ($fila['.id'] === $atributos['.id']) { $fila['comment'] = 'ok:'.(CarbonImmutable::now('UTC')->timestamp + $this->desfase); }
            }
            return [];
        }
        if ($accion === 'add') {
            $this->tablas[$base][] = array_merge(['.id'=>'*'.(++$this->secuencia)], $atributos);
            if ($base === '/ip/hotspot/user' && $this->perderRespuesta) {
                $this->perderRespuesta = false;
                throw new ErrorRouter('Respuesta perdida después de crear el usuario.');
            }
        } elseif ($accion === 'set') {
            foreach ($this->tablas[$base] as &$fila) {
                if ($fila['.id'] === $atributos['.id']) { $fila = array_merge($fila, $atributos); }
            }
        } elseif ($accion === 'remove') {
            $this->tablas[$base] = array_values(array_filter($this->tablas[$base] ?? [], fn ($f) => $f['.id'] !== $atributos['.id']));
        }
        return [];
    }
}

class MikrotikHotspotTest extends TestCase
{
    private RouterSimulado $api;
    private ?bool $permitir = true;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2025_03_16_105935_create_permission_tables.php'))->up();
        (require base_path('Modules/Mikrotik/Database/Migrations/2026_11_03_000001_create_mikrotik_visitantes_tables.php'))->up();
        (require base_path('Modules/Mikrotik/Database/Migrations/2026_11_05_000001_add_inicio_vigencia_to_mikrotik.php'))->up();
        (require base_path('Modules/Mikrotik/Database/Migrations/2026_11_05_000002_add_impresion_to_mt_fichas.php'))->up();
        Gate::before(fn () => $this->permitir);
        Carbon::setTestNow(Carbon::parse('2026-11-03 18:00:00','UTC'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-03 18:00:00','UTC'));
        $this->api = new RouterSimulado();
        $factory = $this->mock(RouterFactory::class);
        $factory->shouldReceive('abrir')->andReturn($this->api);
        $this->withoutExceptionHandling([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function login(): User
    {
        $u = new User(['name'=>'Operador','email'=>'mt@example.test']);
        $u->id = 1;
        $this->actingAs($u);
        return $u;
    }

    private function router(bool $preparar = true): Router
    {
        $r = Router::create(['uuid'=>(string) Str::uuid(),'nombre'=>'Visitantes','host'=>'10.10.200.1','puerto'=>8728,'tls'=>false,
            'usuario_api'=>'gestion','clave_api'=>'CLAVE-API-SECRETA','servidor_hotspot'=>'visitantes','portal_url'=>'http://wifi.example.test/login','ssid'=>'Visitas','velocidad'=>'2M/5M']);
        if ($preparar) { app(HotspotService::class)->preparar($this->api, $r); }
        return $r;
    }

    private function datos(Router $r, int $dias = 1, int $cantidad = 1): array
    {
        return ['router_id'=>$r->id,'dias'=>$dias,'cantidad'=>$cantidad,'visitante'=>'Visita de prueba','solicitud_uuid'=>(string) Str::uuid()];
    }

    private function lote(Router $r, int $dias = 1, int $cantidad = 1, bool $publicar = true): Lote
    {
        $s = app(FichasService::class);
        $l = $s->crear($r, $this->datos($r,$dias,$cantidad), 1);
        $vence = CarbonImmutable::now('UTC')->addDays($dias);
        $l->update(['inicio_vigencia'=>'emision','vence_at'=>$vence]);
        $l->fichas()->update(['inicio_vigencia'=>'emision','vence_at'=>$vence,'duracion_dias'=>null]);
        if ($publicar) { $s->publicar($l, 1); }
        return $l;
    }

    public function test_rutas_y_escrituras_requieren_sesion_y_permisos(): void
    {
        $this->getJson(route('mt.index'))->assertUnauthorized();
        $this->postJson(route('mt.crear'), [])->assertUnauthorized();
        $this->login(); $this->permitir = false;
        foreach (['mt.index','mt.configuracion','mt.fichas','mt.empleados'] as $route) { $this->getJson(route($route))->assertForbidden(); }
        $this->postJson(route('mt.guardar'), [])->assertForbidden();
        $this->postJson(route('mt.crear'), [])->assertForbidden();
        $this->postJson(route('mt.empleados.crear'), [])->assertForbidden();
    }

    public function test_seeder_aditivo_y_acceso_real_administrador(): void
    {
        $rol = Role::create(['name'=>'ADMINISTRADOR','guard_name'=>'web']);
        $permiso = Permission::create(['name'=>'existente','guard_name'=>'web']);
        $rol->givePermissionTo($permiso);
        $this->seed(MikrotikDatabaseSeeder::class);
        $this->seed(MikrotikDatabaseSeeder::class);
        $this->assertTrue($rol->fresh()->hasPermissionTo('existente'));
        $this->assertSame(5, Permission::count());
        $u = $this->login(); $u->exists = true; $u->assignRole('ADMINISTRADOR'); $this->permitir = null;
        $this->get(route('mt.index'))->assertOk();
        $this->get(route('mt.configuracion'))->assertOk();
    }

    public function test_claves_cifradas_y_omitidas_de_serializacion(): void
    {
        $r = $this->router(); $l = $this->lote($r); $f = $l->fichas()->firstOrFail();
        $this->assertNotSame($r->clave_api, DB::table('mt_routers')->value('clave_api'));
        $this->assertNotSame($f->clave, DB::table('mt_fichas')->value('clave'));
        $this->assertArrayNotHasKey('clave_api', $r->toArray());
        $this->assertArrayNotHasKey('clave', $f->toArray());
        $this->assertStringNotContainsString($f->clave, $f->qrUrl());
        $this->assertStringContainsString('username='.$f->usuario, $f->qrUrl());
    }

    public function test_configuracion_conserva_clave_y_no_muestra_secreto(): void
    {
        $this->login();
        $d = ['nombre'=>'MTK','host'=>'10.10.200.1','puerto'=>8728,'tls'=>0,'usuario_api'=>'gestion','password'=>'CLAVE-API-SECRETA',
            'servidor_hotspot'=>'visitantes','portal_url'=>'http://wifi.example.test/login','ssid'=>'Visitas','velocidad'=>'2M/5M'];
        $this->post(route('mt.guardar'),$d)->assertRedirect();
        $r = Router::firstOrFail();
        $this->get(route('mt.configuracion'))->assertOk()->assertDontSee('CLAVE-API-SECRETA');
        $d['password']='';
        $this->put(route('mt.actualizar',$r),$d)->assertRedirect();
        $this->assertSame('CLAVE-API-SECRETA',$r->fresh()->clave_api);
        $d['portal_url']='javascript:alert(1)';
        $this->putJson(route('mt.actualizar',$r),$d)->assertUnprocessable();
        $d['portal_url']='https://wifi.example.test/login?password=secreto';
        $this->putJson(route('mt.actualizar',$r),$d)->assertUnprocessable();
    }

    public function test_no_se_cambia_destino_de_router_con_historial(): void
    {
        $this->login(); $r=$this->router(); $this->lote($r);
        $d=$r->only(['nombre','host','puerto','tls','usuario_api','servidor_hotspot','portal_url','ssid','velocidad']);
        $this->putJson(route('mt.actualizar',$r),$d)->assertRedirect();
        $d['host']='10.10.200.2';
        $this->putJson(route('mt.actualizar',$r),$d)->assertUnprocessable();
        $this->assertSame('10.10.200.1',$r->fresh()->host);
    }

    public function test_preparacion_es_idempotente_y_aislada(): void
    {
        $r=$this->router(); $h=app(HotspotService::class);
        $this->api->tablas['/ip/hotspot/user/profile'][]=['.id'=>'*externo','name'=>'clientes','on-login'=>'CODIGO ORIGINAL'];
        $h->preparar($this->api,$r);
        $this->assertCount(1,$this->api->tablas['/system/script']);
        $this->assertCount(1,$this->api->tablas['/system/scheduler']);
        $this->assertCount(3,$this->api->tablas['/ip/hotspot/user/profile']);
        $this->assertSame('CODIGO ORIGINAL',collect($this->api->tablas['/ip/hotspot/user/profile'])->firstWhere('name','clientes')['on-login']);
        $this->assertSame('30s',$this->api->tablas['/system/scheduler'][0]['interval']);
        $this->assertStringContainsString('/system clock get date',ScriptsHotspot::login($r));
        $this->assertStringContainsString('comment~"^'.$r->marcador().':"',ScriptsHotspot::barrido($r));
        foreach ($this->api->escrituras as [$ruta]) {
            $this->assertFalse(str_contains($ruta,'dhcp') || str_contains($ruta,'firewall') || str_contains($ruta,'reboot'));
        }
    }

    public function test_version_no_compatible_se_rechaza_antes_de_escribir(): void
    {
        $r=$this->router(false); $this->api->tablas['/system/resource'][0]['version']='6.48.7';
        try { app(HotspotService::class)->preparar($this->api,$r); $this->fail('Debió rechazar la versión.'); }
        catch (ErrorRouter $e) { $this->assertStringContainsString('6.49',$e->getMessage()); }
        $this->assertCount(0,$this->api->escrituras);
    }

    public function test_prepara_y_emite_en_routeros_64923_y_7_con_api_simulada(): void
    {
        foreach (['6.49.23 (long-term)', '7.1 (stable)', '7.16.2 (stable)'] as $version) {
            $this->api->tablas['/system/resource'][0]['version']=$version;
            $r=$this->router();
            $this->assertSame($version,$r->fresh()->version);
            $f=$this->lote($r)->fichas()->firstOrFail();
            $this->assertSame('activa',$f->estado);
            $this->assertTrue($f->imprimible());
            foreach ([ScriptsHotspot::login($r), ScriptsHotspot::barrido($r)] as $script) {
                $this->assertStringNotContainsString(':timestamp',$script);
                $this->assertStringNotContainsString(':tonsec',$script);
                $this->assertStringContainsString('/system clock get gmt-offset',$script);
                $this->assertStringContainsString('janfebmar',$script);
                $this->assertStringNotContainsString('@RELOJ@',$script);
            }
        }
    }

    public function test_fichas_de_1_2_y_7_dias_usando_hora_utc(): void
    {
        $r=$this->router();
        foreach ([1,2,7] as $dias) {
            $l=$this->lote($r,$dias,2);
            $this->assertSame(CarbonImmutable::now('UTC')->addDays($dias)->timestamp,$l->vence_at->timestamp);
            foreach ($l->fichas as $f) {
                $this->assertSame('activa',$f->estado);
                $this->assertTrue($f->imprimible());
                $this->assertSame(12,strlen($f->clave));
                $usuario=collect($this->api->tablas['/ip/hotspot/user'])->firstWhere('name',$f->usuario);
                $this->assertSame($r->marcador().':'.$f->vence_at->timestamp,$usuario['comment']);
                $this->assertSame($r->perfil(),$usuario['profile']);
                $this->assertSame('no',$usuario['disabled']);
                $this->assertArrayNotHasKey('limit-uptime',$usuario);
            }
        }
        $this->assertSame(6,Ficha::count());
    }

    public function test_doble_envio_no_duplica_lote_usuarios_o_vigencia(): void
    {
        $r=$this->router(); $d=$this->datos($r,2,3); $s=app(FichasService::class);
        $a=$s->crear($r,$d,1); $s->publicar($a,1);
        $b=$s->crear($r,$d,1); $s->publicar($b,1);
        $this->assertSame($a->id,$b->id);
        $this->assertSame(1,Lote::count()); $this->assertSame(3,Ficha::count());
        $this->assertCount(3,$this->api->tablas['/ip/hotspot/user']);
        $d['dias']=7;
        $this->expectException(ValidationException::class); $s->crear($r,$d,1);
    }

    public function test_respuesta_perdida_se_recupera_sin_duplicar_usuario(): void
    {
        $r=$this->router(); $this->api->perderRespuesta=true;
        $l=$this->lote($r); $f=$l->fichas()->firstOrFail();
        $this->assertSame('error',$f->estado);
        $this->assertSame('yes',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
        app(FichasService::class)->publicar($l,1);
        $this->assertCount(1,$this->api->tablas['/ip/hotspot/user']);
        $this->assertSame('activa',$f->fresh()->estado);
    }

    public function test_interrupcion_de_api_detiene_el_lote_y_reintento_lo_completa(): void
    {
        $r=$this->router(); $this->api->perderRespuesta=true;
        $l=$this->lote($r,1,3);
        $this->assertCount(1,$this->api->tablas['/ip/hotspot/user']);
        $this->assertSame(3,$l->fichas()->where('estado','error')->count());
        app(FichasService::class)->publicar($l,1);
        $this->assertCount(3,$this->api->tablas['/ip/hotspot/user']);
        $this->assertSame(3,$l->fichas()->where('estado','activa')->count());
    }

    public function test_sin_preparacion_o_con_reloj_desfasado_no_habilita_fichas(): void
    {
        $r=$this->router(false); $l=$this->lote($r);
        $this->assertSame('error',$l->fichas()->firstOrFail()->estado);
        $this->assertArrayNotHasKey('/ip/hotspot/user',$this->api->tablas);
        app(HotspotService::class)->preparar($this->api,$r);
        $this->api->desfase=3600;
        app(FichasService::class)->publicar($l,1);
        $this->assertArrayNotHasKey('/ip/hotspot/user',$this->api->tablas);
    }

    public function test_proteccion_alterada_impide_emitir(): void
    {
        $r=$this->router();
        $this->api->tablas['/system/scheduler'][0]['disabled']='yes';
        $l=$this->lote($r);
        $this->assertSame('error',$l->fichas()->firstOrFail()->estado);
        $this->assertArrayNotHasKey('/ip/hotspot/user',$this->api->tablas);
    }

    public function test_no_modifica_usuario_ajeno_con_nombre_coincidente(): void
    {
        $r=$this->router(); $l=$this->lote($r,1,1,false); $f=$l->fichas()->firstOrFail();
        $this->api->tablas['/ip/hotspot/user']=[['.id'=>'*ajeno','name'=>$f->usuario,'comment'=>'Cliente existente','profile'=>'default','server'=>'visitantes','disabled'=>'no']];
        app(FichasService::class)->publicar($l,1);
        $this->assertSame('Cliente existente',$this->api->tablas['/ip/hotspot/user'][0]['comment']);
        $this->assertSame('error',$f->fresh()->estado);
    }

    public function test_revocar_desconecta_y_elimina_cookie_solo_de_su_usuario(): void
    {
        $r=$this->router(); $l=$this->lote($r); $f=$l->fichas()->firstOrFail();
        $this->api->tablas['/ip/hotspot/active']=[['.id'=>'*A','user'=>$f->usuario,'server'=>'visitantes'],['.id'=>'*B','user'=>'cliente-ajeno','server'=>'visitantes']];
        $this->api->tablas['/ip/hotspot/cookie']=[['.id'=>'*C','user'=>$f->usuario],['.id'=>'*D','user'=>'cliente-ajeno']];
        app(FichasService::class)->revocar($f,1);
        $this->assertSame('revocada',$f->fresh()->estado);
        $this->assertSame('yes',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
        $this->assertSame('cliente-ajeno',$this->api->tablas['/ip/hotspot/active'][0]['user']);
        $this->assertSame('cliente-ajeno',$this->api->tablas['/ip/hotspot/cookie'][0]['user']);
        app(FichasService::class)->publicar($l,1,true);
        $this->assertSame('yes',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
    }

    public function test_revocacion_offline_se_conserva_y_concilia_al_volver(): void
    {
        $r=$this->router(); $f=$this->lote($r)->fichas()->firstOrFail();
        $this->api->offline=true;
        app(FichasService::class)->revocar($f,1);
        $this->assertSame('revocacion_pendiente',$f->fresh()->estado);
        $this->assertNotNull($f->fresh()->revocada_at);
        $this->assertFalse($f->fresh()->imprimible());
        $this->api->offline=false;
        $this->artisan('mikrotik:conciliar')->assertSuccessful();
        $this->assertSame('revocada',$f->fresh()->estado);
    }

    public function test_vencida_no_se_publica_y_conciliacion_la_bloquea(): void
    {
        $r=$this->router(); $l=$this->lote($r); $f=$l->fichas()->firstOrFail();
        Carbon::setTestNow(Carbon::parse('2026-11-04 18:00:01','UTC'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-04 18:00:01','UTC'));
        $this->assertFalse($f->imprimible());
        app(FichasService::class)->conciliar($r->id);
        $this->assertSame('vencida',$f->fresh()->estado);
        app(FichasService::class)->publicar($l,1,true);
        $this->assertSame('yes',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
    }

    public function test_validaciones_del_lote_y_flujo_desde_formularios(): void
    {
        $this->login(); $r=$this->router(); $d=$this->datos($r,7,2);
        $this->post(route('mt.crear'),$d)->assertRedirect();
        $this->assertSame(2,Ficha::count());
        $this->post(route('mt.crear'),$d)->assertRedirect();
        $this->assertSame(2,Ficha::count());
        $d['solicitud_uuid']=(string) Str::uuid(); $d['dias']=3;
        $this->postJson(route('mt.crear'),$d)->assertUnprocessable();
        $d['dias']=1; $d['cantidad']=21;
        $this->postJson(route('mt.crear'),$d)->assertUnprocessable();
    }

    public function test_todas_las_pantallas_y_qr_imprimible(): void
    {
        $this->login(); $r=$this->router(); $l=$this->lote($r,2,2); $f=$l->fichas()->firstOrFail();
        foreach (['mt.index','mt.fichas','mt.configuracion','mt.sesiones.routers'] as $route) { $this->get(route($route))->assertOk(); }
        foreach (['mt.estado','mt.preparacion','mt.sesiones'] as $route) { $this->get(route($route,$r))->assertOk(); }
        $this->get(route('mt.lote',$l))->assertOk()->assertSee($f->usuario);
        $this->get(route('mt.imprimir',$l))->assertOk()->assertSee('<svg',false)->assertSee($f->clave)->assertSee('05/11/2026 12:00:00')->assertHeader('Cache-Control','no-store, private');
        $otro=$this->lote($r)->fichas()->firstOrFail();
        $this->get(route('mt.imprimir',['lote'=>$l,'ficha_id'=>$otro->id]))->assertNotFound();
        app(FichasService::class)->revocar($f,1);
        $this->get(route('mt.imprimir',['lote'=>$l,'ficha_id'=>$f->id]))->assertUnprocessable();
        $this->get(route('mt.imprimir',$l))->assertOk()->assertSee('Se omitieron 1 fichas')->assertDontSee($f->clave);
    }

    public function test_fichas_y_sesiones_no_otorgan_permiso_de_configurar(): void
    {
        $this->seed(MikrotikDatabaseSeeder::class);
        $u=$this->login(); $u->exists=true; $u->givePermissionTo('mikrotik.fichas'); $this->permitir=null;
        $r=$this->router();
        $this->get(route('mt.fichas'))->assertOk();
        $this->getJson(route('mt.configuracion'))->assertForbidden();
        $this->postJson(route('mt.preparar',$r),['confirmar'=>1])->assertForbidden();
        $this->getJson(route('mt.sesiones',$r))->assertForbidden();
    }

    private function activarEnRouter(Ficha $f, int $inicio): string
    {
        // Simula el resultado persistido de on-login, sin afirmar que ejecuta RouterOS.
        $comentario = $f->router->marcador().':A'.$f->duracion_dias.':'.$inicio.':'.($inicio + $f->duracion_dias * 86400);
        foreach ($this->api->tablas['/ip/hotspot/user'] as &$u) {
            if ($u['name'] === $f->usuario) { $u['comment'] = $comentario; }
        }
        return $comentario;
    }

    public function test_nuevas_fichas_no_vencen_antes_del_primer_ingreso_ni_al_imprimir(): void
    {
        $this->login(); $r=$this->router(); $s=app(FichasService::class);
        foreach ([1,2,7] as $dias) {
            $l=$s->crear($r,$this->datos($r,$dias),1); $s->publicar($l,1);
            $f=$l->fichas()->firstOrFail();
            $this->assertNull($l->vence_at); $this->assertNull($f->vence_at); $this->assertNull($f->activada_at);
            $u=collect($this->api->tablas['/ip/hotspot/user'])->firstWhere('name',$f->usuario);
            $this->assertSame($r->marcador().':P'.$dias,$u['comment']);
            $this->get(route('mt.imprimir',$l))->assertOk()->assertSee('Imprimir no activa ni reinicia el plazo');
            $this->assertNull($f->fresh()->activada_at);
        }
        Carbon::setTestNow(Carbon::parse('2026-12-03 18:00:00','UTC'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-12-03 18:00:00','UTC'));
        $s->conciliar($r->id);
        foreach (Ficha::all() as $f) { $this->assertTrue($f->imprimible()); $this->assertNull($f->vence_at); }
    }

    public function test_primer_ingreso_individual_se_sincroniza_y_reintentar_no_lo_reinicia(): void
    {
        $this->login(); $r=$this->router(); $s=app(FichasService::class);
        foreach ([1,2,7] as $dias) {
            $l=$s->crear($r,$this->datos($r,$dias,2),1); $s->publicar($l,1);
            $f=$l->fichas()->firstOrFail(); $inicio=CarbonImmutable::now('UTC')->timestamp;
            $comentario=$this->activarEnRouter($f,$inicio);
            $this->assertSame(0,$s->conciliar($r->id)); $f->refresh();
            $this->assertSame($inicio,$f->activada_at->timestamp);
            $this->assertSame($inicio+$dias*86400,$f->vence_at->timestamp);
            $this->assertNull($l->fichas()->orderByDesc('id')->first()->vence_at);
            $s->publicar($l,1,true);
            $u=collect($this->api->tablas['/ip/hotspot/user'])->firstWhere('name',$f->usuario);
            $this->assertSame($comentario,$u['comment']);
            $this->get(route('mt.imprimir',['lote'=>$l,'ficha_id'=>$f->id]))->assertOk();
            $this->assertSame($inicio,$f->fresh()->activada_at->timestamp);
        }
    }

    public function test_no_recrea_usuario_activado_ni_acepta_reinicio_o_extension(): void
    {
        $r=$this->router(); $s=app(FichasService::class);
        $l=$s->crear($r,$this->datos($r),1); $s->publicar($l,1); $f=$l->fichas()->firstOrFail();
        $inicio=CarbonImmutable::now('UTC')->timestamp; $this->activarEnRouter($f,$inicio); $s->conciliar($r->id);
        foreach ([$r->marcador().':P1',$r->marcador().':A1:'.($inicio+1).':'.($inicio+86401),$r->marcador().':E'] as $incorrecto) {
            $this->api->tablas['/ip/hotspot/user'][0]['comment']=$incorrecto;
            $this->assertSame(1,$s->publicar($l,1,true)['errores']);
            $this->assertSame($inicio,$f->fresh()->activada_at->timestamp);
        }
        $this->api->tablas['/ip/hotspot/user']=[];
        $this->assertSame(1,$s->publicar($l,1,true)['errores']);
        $this->assertCount(0,$this->api->tablas['/ip/hotspot/user']);
    }

    public function test_no_recrea_pendiente_publicada_aunque_un_error_haya_ocultado_su_estado(): void
    {
        $r=$this->router(); $s=app(FichasService::class);
        $l=$s->crear($r,$this->datos($r),1); $s->publicar($l,1);
        $f=$l->fichas()->firstOrFail(); $this->assertNotNull($f->sincronizada_at);
        $this->api->offline=true; $s->publicar($l,1,true);
        $this->assertSame('error',$f->fresh()->estado);
        $this->api->offline=false; $this->api->tablas['/ip/hotspot/user']=[];
        $this->assertSame(1,$s->publicar($l,1,true)['errores']);
        $this->assertCount(0,$this->api->tablas['/ip/hotspot/user']);
    }

    public function test_ficha_activada_caduca_aunque_se_desconecte_y_no_se_rehabilita(): void
    {
        $r=$this->router(); $s=app(FichasService::class);
        $l=$s->crear($r,$this->datos($r),1); $s->publicar($l,1); $f=$l->fichas()->firstOrFail();
        $this->activarEnRouter($f,CarbonImmutable::now('UTC')->timestamp);
        Carbon::setTestNow(Carbon::parse('2026-11-04 18:00:00','UTC'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-04 18:00:00','UTC'));
        $s->conciliar($r->id); $this->assertSame('vencida',$f->fresh()->estado);
        $this->assertFalse($f->fresh()->imprimible());
        $s->publicar($l,1,true); $this->assertSame('yes',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
    }

    public function test_empleados_sin_vencimiento_separados_revocables_y_sin_escalado_por_formulario(): void
    {
        $this->login(); $r=$this->router(); $s=app(FichasService::class); $d=$this->datos($r);
        $this->post(route('mt.empleados.crear'),$d)->assertRedirect();
        $this->post(route('mt.empleados.crear'),$d)->assertRedirect();
        $this->assertSame(1,Ficha::count()); $f=Ficha::firstOrFail();
        $this->assertSame('permanente',$f->inicio_vigencia); $this->assertNull($f->vence_at);
        $this->assertSame($r->marcador().':E',$this->api->tablas['/ip/hotspot/user'][0]['comment']);
        $this->get(route('mt.empleados'))->assertOk()->assertSee($f->usuario)->assertSee('Sin vencimiento');
        $this->get(route('mt.fichas'))->assertOk()->assertDontSee($f->usuario);
        $this->get(route('mt.imprimir',$f->lote))->assertOk()->assertSee('Internet para empleados')->assertSee('Sin vencimiento');
        $d['solicitud_uuid']=(string) Str::uuid(); $d['dias']=0;
        $this->postJson(route('mt.crear'),$d)->assertUnprocessable();
        $d['visitante']=''; $this->postJson(route('mt.empleados.crear'),$d)->assertUnprocessable();
        Carbon::setTestNow(Carbon::parse('2030-11-03 18:00:00','UTC'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2030-11-03 18:00:00','UTC'));
        $s->conciliar($r->id); $this->assertTrue($f->fresh()->imprimible());
        $s->revocar($f,1); $this->assertSame('revocada',$f->fresh()->estado);
        $this->assertSame('yes',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
    }

    public function test_impresion_seleccionada_solo_incluye_fichas_del_lote_y_conserva_vigencia(): void
    {
        $this->login(); $r=$this->router(); $l=$this->lote($r,2,3);
        [$a,$b,$c]=$l->fichas()->orderBy('id')->get()->all();
        $estado=$a->only(['estado','vence_at','activada_at','sincronizada_at']);
        $escrituras=count($this->api->escrituras);
        $this->get(route('mt.lote',$l))->assertOk()->assertSee('Imprimir lote completo (3)')->assertSee('Imprimir seleccionadas');
        $url=route('mt.imprimir',['lote'=>$l,'seleccion'=>1,'fichas'=>[$a->id,$c->id]]);
        $this->get($url)->assertOk()->assertSee($a->clave)->assertSee($c->clave)->assertDontSee($b->clave)
            ->assertSee('2 fichas listas para imprimir')->assertHeader('Cache-Control','no-store, private');
        $this->assertEquals($estado,$a->fresh()->only(array_keys($estado)));
        $this->assertSame($escrituras,count($this->api->escrituras));
        $otro=$this->lote($r)->fichas()->firstOrFail();
        $this->get(route('mt.imprimir',['lote'=>$l,'fichas'=>[$a->id,$otro->id]]))->assertNotFound();
        $this->getJson(route('mt.imprimir',['lote'=>$l,'seleccion'=>1]))->assertUnprocessable();
        $this->getJson(route('mt.imprimir',['lote'=>$l,'fichas'=>[$a->id,$a->id]]))->assertUnprocessable();
        $this->getJson(route('mt.imprimir',['lote'=>$l,'ficha_id'=>$a->id,'fichas'=>[$c->id]]))->assertUnprocessable();
        app(FichasService::class)->revocar($c,1);
        $this->get($url)->assertOk()->assertSee($a->clave)->assertDontSee($c->clave)->assertSee('Se omitieron 1 fichas');
        $this->get(route('mt.imprimir',['lote'=>$l,'fichas'=>[$c->id]]))->assertUnprocessable();
    }

    public function test_imprime_nombre_del_empleado_escapado_en_ficha_individual_y_lote(): void
    {
        $this->login(); $r=$this->router(); $d=$this->datos($r);
        $d['visitante']='Luis Díaz <script>alert(1)</script>'; $d['dias']=0; $d['cantidad']=1;
        $l=app(FichasService::class)->crear($r,$d,1,true);
        app(FichasService::class)->publicar($l,1); $f=$l->fichas()->firstOrFail();
        foreach ([['lote'=>$l],['lote'=>$l,'ficha_id'=>$f->id],['lote'=>$l,'seleccion'=>1,'fichas'=>[$f->id]]] as $parametros) {
            $this->get(route('mt.imprimir',$parametros))->assertOk()->assertSee('Empleado')->assertSee($d['visitante'])
                ->assertDontSee('<script>alert(1)</script>',false)->assertSee('Sin vencimiento')->assertSee($f->clave);
        }
        $this->assertNull($f->fresh()->activada_at);
        $this->assertNull($f->fresh()->vence_at);
    }

    public function test_panel_imprime_activas_sin_imprimir_de_varios_lotes_y_registra_confirmacion(): void
    {
        $this->login(); $r=$this->router();
        $a=$this->lote($r)->fichas()->firstOrFail(); $b=$this->lote($r)->fichas()->firstOrFail();
        $impresa=$this->lote($r)->fichas()->firstOrFail(); $impresa->update(['impresa_at'=>CarbonImmutable::now('UTC')->subHour(),'impresa_por'=>1]);
        $error=$this->lote($r)->fichas()->firstOrFail(); $error->update(['estado'=>'error']);
        $revocada=$this->lote($r)->fichas()->firstOrFail(); app(FichasService::class)->revocar($revocada,1);
        $antes=$a->only(['estado','vence_at','activada_at','sincronizada_at']); $escrituras=count($this->api->escrituras);
        $url=route('mt.fichas.imprimir',['empleados'=>0,'modo'=>'pendientes']);
        $this->get($url)->assertOk()->assertSee($a->clave)->assertSee($b->clave)->assertDontSee($impresa->clave)
            ->assertDontSee($error->clave)->assertDontSee($revocada->clave)->assertSee('Confirmar impresas');
        $this->assertNull($a->fresh()->impresa_at); $this->assertNull($b->fresh()->impresa_at);
        $this->assertSame($escrituras,count($this->api->escrituras));
        $this->post(route('mt.fichas.impresas'),['seleccion_impresa'=>json_encode([$a->id,$b->id])])->assertRedirect(route('mt.fichas'));
        $this->assertNotNull($a->fresh()->impresa_at); $this->assertSame(1,(int)$a->fresh()->impresa_por);
        $fecha=$a->fresh()->impresa_at->timestamp;
        $this->post(route('mt.fichas.impresas'),['seleccion_impresa'=>json_encode([$a->id])])->assertRedirect();
        $this->assertSame($fecha,$a->fresh()->impresa_at->timestamp);
        $this->assertEquals($antes,$a->fresh()->only(array_keys($antes)));
        $this->assertSame($escrituras,count($this->api->escrituras));
        $this->get($url)->assertUnprocessable();
        $this->get(route('mt.fichas.imprimir',['empleados'=>0,'modo'=>'todas']))->assertOk()->assertSee($a->clave)->assertSee($impresa->clave);
    }

    public function test_seleccion_general_rechaza_ajenas_y_omite_impresas_y_no_activas(): void
    {
        $this->login(); $r=$this->router(); $a=$this->lote($r)->fichas()->firstOrFail(); $b=$this->lote($r)->fichas()->firstOrFail();
        $b->update(['impresa_at'=>CarbonImmutable::now('UTC')]);
        $this->get(route('mt.fichas.imprimir',['empleados'=>0,'modo'=>'seleccion','fichas'=>[$a->id,$b->id]]))
            ->assertOk()->assertSee($a->clave)->assertDontSee($b->clave)->assertSee('Se omitieron 1 fichas');
        $r2=$this->router(); $ajena=$this->lote($r2)->fichas()->firstOrFail();
        $this->get(route('mt.fichas.imprimir',['empleados'=>0,'router_id'=>$r->id,'modo'=>'seleccion','fichas'=>[$ajena->id]]))->assertNotFound();
        $this->getJson(route('mt.fichas.imprimir',['empleados'=>0,'modo'=>'seleccion']))->assertUnprocessable();
        $this->getJson(route('mt.fichas.imprimir',['empleados'=>0,'modo'=>'seleccion','fichas'=>[$a->id,$a->id]]))->assertUnprocessable();
        $this->get(route('mt.fichas',['impresion'=>'pendientes']))->assertOk()->assertSee($a->usuario)->assertDontSee($b->usuario);
        $this->get(route('mt.fichas',['impresion'=>'impresas']))->assertOk()->assertSee($b->usuario)->assertDontSee($a->usuario);
    }

    public function test_edita_nombre_por_ficha_y_lo_imprime_sin_alterar_acceso(): void
    {
        $this->login(); $r=$this->router(); $l=$this->lote($r,1,2); [$a,$b]=$l->fichas()->get()->all();
        $antes=$a->only(['usuario','clave','estado','vence_at','activada_at']); $escrituras=count($this->api->escrituras);
        $this->get(route('mt.fichas.editar',$a))->assertOk();
        $this->put(route('mt.fichas.actualizar',$a),['nombre'=>'Luis Díaz <b>Empleado</b>','dias'=>7,'estado'=>'activa'])->assertRedirect();
        $this->assertSame('Luis Díaz <b>Empleado</b>',$a->fresh()->nombreTitular());
        $this->assertSame('Visita de prueba',$b->fresh()->nombreTitular());
        $this->get(route('mt.imprimir',['lote'=>$l,'ficha_id'=>$a->id]))->assertOk()->assertSee('Luis Díaz <b>Empleado</b>')->assertDontSee('<b>Empleado</b>',false);
        $this->assertEquals($antes,$a->fresh()->only(array_keys($antes))); $this->assertSame($escrituras,count($this->api->escrituras));
        $this->get(route('mt.fichas',['q'=>'Luis Díaz']))->assertOk()->assertSee($a->usuario)->assertDontSee($b->usuario);
    }

    public function test_elimina_errores_solo_tras_bloquear_y_mantiene_historial(): void
    {
        $this->login(); $r=$this->router(); $l=$this->lote($r); $f=$l->fichas()->firstOrFail(); $f->update(['estado'=>'error']);
        $activa=$this->lote($r)->fichas()->firstOrFail();
        $this->api->tablas['/ip/hotspot/active']=[['.id'=>'*ACT','user'=>$f->usuario,'server'=>'visitantes']];
        $d=['empleados'=>0,'router_id'=>$r->id,'confirmar'=>1];
        $this->deleteJson(route('mt.fichas.eliminar-errores'),['empleados'=>0])->assertUnprocessable();
        $this->delete(route('mt.fichas.eliminar-errores'),$d)->assertRedirect();
        $this->assertNull(Ficha::find($f->id)); $this->assertNotNull(Ficha::withTrashed()->find($f->id)->deleted_at);
        $this->assertNotNull(Lote::find($l->id)); $this->assertNotNull(Ficha::find($activa->id));
        $this->assertSame('yes',collect($this->api->tablas['/ip/hotspot/user'])->firstWhere('name',$f->usuario)['disabled']);
        $this->assertCount(0,$this->api->tablas['/ip/hotspot/active']);
        app(FichasService::class)->publicar($l,1,true);
        $this->assertSame('yes',collect($this->api->tablas['/ip/hotspot/user'])->firstWhere('name',$f->usuario)['disabled']);
        $this->assertTrue(DB::table('mt_eventos')->where('ficha_id',$f->id)->where('accion','eliminar_ficha_error')->where('correcto',true)->exists());
    }

    public function test_no_elimina_error_con_router_offline_ni_usuario_ajeno(): void
    {
        $this->login(); $r=$this->router(); $f=$this->lote($r)->fichas()->firstOrFail(); $f->update(['estado'=>'error']);
        $d=['empleados'=>0,'confirmar'=>1]; $this->api->offline=true;
        $this->delete(route('mt.fichas.eliminar-errores'),$d)->assertRedirect();
        $this->assertSame('error',$f->fresh()->estado);
        $this->api->offline=false; $this->api->tablas['/ip/hotspot/user'][0]['comment']='usuario ajeno';
        $this->delete(route('mt.fichas.eliminar-errores'),$d)->assertRedirect();
        $this->assertSame('error',$f->fresh()->estado); $this->assertSame('no',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
    }

    public function test_acciones_de_impresion_edicion_y_eliminacion_exigen_permiso(): void
    {
        $this->postJson(route('mt.fichas.impresas'),['fichas'=>[1]])->assertUnauthorized();
        $this->login(); $r=$this->router(); $f=$this->lote($r)->fichas()->firstOrFail(); $this->permitir=false;
        $this->getJson(route('mt.fichas.imprimir',['empleados'=>0,'modo'=>'todas']))->assertForbidden();
        $this->postJson(route('mt.fichas.impresas'),['fichas'=>[$f->id]])->assertForbidden();
        $this->getJson(route('mt.fichas.editar',$f))->assertForbidden();
        $this->putJson(route('mt.fichas.actualizar',$f),['nombre'=>'Otro'])->assertForbidden();
        $this->deleteJson(route('mt.fichas.eliminar-errores'),['empleados'=>0,'confirmar'=>1])->assertForbidden();
    }

    private function empleado(Router $r): Ficha
    {
        $d=$this->datos($r); $d['dias']=0;
        $l=app(FichasService::class)->crear($r,$d,1,true);
        app(FichasService::class)->publicar($l,1);
        return $l->fichas()->firstOrFail();
    }

    public function test_perfil_empleados_5m_independiente_de_visitantes_y_sin_vencimiento(): void
    {
        $r=$this->router(); $r->update(['velocidad'=>'1M/2M']); app(HotspotService::class)->preparar($this->api,$r);
        $f=$this->empleado($r); $v=$this->lote($r)->fichas()->firstOrFail();
        $perfiles=collect($this->api->tablas['/ip/hotspot/user/profile'])->keyBy('name');
        $this->assertSame('5M/5M',$perfiles[$r->perfilEmpleados()]['rate-limit']);
        $this->assertSame('1M/2M',$perfiles[$r->perfil()]['rate-limit']);
        $usuarios=collect($this->api->tablas['/ip/hotspot/user'])->keyBy('name');
        $this->assertSame($r->perfilEmpleados(),$usuarios[$f->usuario]['profile']);
        $this->assertSame($r->perfil(),$usuarios[$v->usuario]['profile']);
        $this->assertSame($r->marcador().':E',$usuarios[$f->usuario]['comment']);
        $this->assertNull($f->vence_at); $this->assertNull($f->activada_at);
        $this->assertArrayNotHasKey('limit-uptime',$usuarios[$f->usuario]);
        $this->assertSame('1',$perfiles[$r->perfilEmpleados()]['shared-users']);
    }

    public function test_preparar_traslada_empleados_anteriores_conservando_credenciales_y_revocacion(): void
    {
        $r=$this->router(); $a=$this->empleado($r); $b=$this->empleado($r); $v=$this->lote($r)->fichas()->firstOrFail();
        app(FichasService::class)->revocar($b,1);
        foreach ($this->api->tablas['/ip/hotspot/user'] as &$u) {
            if (in_array($u['name'],[$a->usuario,$b->usuario],true)) { $u['profile']=$r->perfil(); }
        }
        unset($u);
        $antes=$this->api->tablas['/ip/hotspot/user'];
        $this->api->tablas['/ip/hotspot/active']=[['.id'=>'*EA','user'=>$a->usuario,'server'=>'visitantes'],['.id'=>'*EV','user'=>$v->usuario,'server'=>'visitantes']];
        $this->api->tablas['/ip/hotspot/cookie']=[['.id'=>'*EC','user'=>$a->usuario]];
        app(HotspotService::class)->preparar($this->api,$r);
        foreach ($this->api->tablas['/ip/hotspot/user'] as $i=>$actual) {
            $previo=$antes[$i];
            if ($actual['name'] !== $v->usuario) { $previo['profile']=$r->perfilEmpleados(); }
            $this->assertSame($previo,$actual);
        }
        $this->assertSame('revocada',$b->fresh()->estado); $this->assertNull($a->fresh()->vence_at);
        $this->assertSame($v->usuario,$this->api->tablas['/ip/hotspot/active'][0]['user']);
        $this->assertCount(0,$this->api->tablas['/ip/hotspot/cookie']);
        app(HotspotService::class)->preparar($this->api,$r);
        $this->assertCount(2,$this->api->tablas['/ip/hotspot/user/profile']);
    }

    public function test_no_publica_con_perfil_empleados_alterado_ni_traslada_usuario_ajeno(): void
    {
        $r=$this->router(); $f=$this->empleado($r);
        foreach ($this->api->tablas['/ip/hotspot/user/profile'] as &$perfil) {
            if ($perfil['name'] === $r->perfilEmpleados()) { $perfil['rate-limit']='20M/20M'; }
        }
        unset($perfil);
        $this->assertSame(1,app(FichasService::class)->publicar($f->lote,1,true)['errores']);
        $this->api->tablas['/ip/hotspot/user'][0]['profile']=$r->perfil();
        $this->api->tablas['/ip/hotspot/user'][0]['comment']='AJENO';
        $previo=$this->api->tablas['/ip/hotspot/user'][0];
        try { app(HotspotService::class)->preparar($this->api,$r); $this->fail('No debe trasladar un usuario ajeno.'); }
        catch (ErrorRouter $e) { $this->assertSame($previo,$this->api->tablas['/ip/hotspot/user'][0]); }
    }

    public function test_preparacion_incompleta_no_conserva_estado_preparado_anterior(): void
    {
        $this->login(); $r=$this->router(); $f=$this->empleado($r);
        $this->api->tablas['/ip/hotspot/user'][0]['profile']=$r->perfil();
        $this->api->tablas['/ip/hotspot/user'][0]['comment']='AJENO';
        $this->post(route('mt.preparar',$r),['confirmar'=>1])->assertRedirect()->assertSessionHasErrors('router');
        $this->assertNull($r->fresh()->preparado_at);
        $this->assertSame(1,app(FichasService::class)->publicar($f->lote,1,true)['errores']);
        $this->assertSame('AJENO',$this->api->tablas['/ip/hotspot/user'][0]['comment']);
    }

    public function test_elimina_seleccion_de_errores_y_revocadas_sin_tocar_otras(): void
    {
        $this->login(); $r=$this->router(); $l=$this->lote($r,1,4); [$error,$revocada,$otra,$activa]=$l->fichas()->get()->all();
        $error->update(['estado'=>'error']); $otra->update(['estado'=>'error']); app(FichasService::class)->revocar($revocada,1);
        $d=['empleados'=>0,'modo'=>'seleccion','estados'=>'error','confirmar'=>1,'fichas'=>[$error->id,$revocada->id]];
        $this->delete(route('mt.fichas.eliminar'),$d)->assertRedirect();
        $this->assertNull(Ficha::find($error->id)); $this->assertNull(Ficha::find($revocada->id));
        $this->assertNotNull(Ficha::find($otra->id)); $this->assertNotNull(Ficha::find($activa->id));
        $this->assertNotNull(Ficha::withTrashed()->find($revocada->id)->deleted_at);
        $this->assertTrue(DB::table('mt_eventos')->where('ficha_id',$revocada->id)->where('accion','eliminar_ficha_revocada')->exists());
    }

    public function test_borrado_masivo_por_categoria_y_alcance(): void
    {
        $this->login(); $r=$this->router();
        foreach (['error','revocada','ambos'] as $categoria) {
            $e=$this->lote($r)->fichas()->firstOrFail(); $e->update(['estado'=>'error']);
            $v=$this->lote($r)->fichas()->firstOrFail(); app(FichasService::class)->revocar($v,1);
            $empleado=$this->empleado($r); $empleado->update(['estado'=>'error']);
            $otroRouter=$this->router(); $otra=$this->lote($otroRouter)->fichas()->firstOrFail(); $otra->update(['estado'=>'error']);
            $d=['empleados'=>0,'router_id'=>$r->id,'modo'=>'todos','estados'=>$categoria,'confirmar'=>1];
            $this->delete(route('mt.fichas.eliminar'),$d)->assertRedirect();
            $this->assertSame($categoria==='revocada',Ficha::find($e->id)!==null);
            $this->assertSame($categoria==='error',Ficha::find($v->id)!==null);
            $this->assertNotNull(Ficha::find($empleado->id)); $this->assertNotNull(Ficha::find($otra->id));
        }
    }

    public function test_borrado_rechaza_activas_revocaciones_pendientes_y_seleccion_invalida(): void
    {
        $this->login(); $r=$this->router(); $activa=$this->lote($r)->fichas()->firstOrFail();
        $pendiente=$this->lote($r)->fichas()->firstOrFail(); $pendiente->update(['estado'=>'revocacion_pendiente','revocada_at'=>CarbonImmutable::now('UTC')]);
        $d=['empleados'=>0,'modo'=>'seleccion','confirmar'=>1];
        foreach ([$activa,$pendiente] as $f) {
            $d['fichas']=[$f->id]; $this->deleteJson(route('mt.fichas.eliminar'),$d)->assertUnprocessable();
            $this->assertNotNull(Ficha::find($f->id));
        }
        unset($d['fichas']); $this->deleteJson(route('mt.fichas.eliminar'),$d)->assertUnprocessable();
        $d['fichas']=[$activa->id,$activa->id]; $this->deleteJson(route('mt.fichas.eliminar'),$d)->assertUnprocessable();
        $d['fichas']=[$activa->id]; $d['empleados']=1; $this->deleteJson(route('mt.fichas.eliminar'),$d)->assertNotFound();
        $this->permitir=false; $this->deleteJson(route('mt.fichas.eliminar'),$d)->assertForbidden();
    }

    public function test_borrado_revocada_reconfirma_bloqueo_y_conserva_si_no_hay_conexion(): void
    {
        $this->login(); $r=$this->router(); $f=$this->lote($r)->fichas()->firstOrFail(); app(FichasService::class)->revocar($f,1);
        $d=['empleados'=>0,'modo'=>'todos','estados'=>'revocada','confirmar'=>1];
        $this->api->offline=true; $this->delete(route('mt.fichas.eliminar'),$d)->assertRedirect();
        $this->assertNotNull(Ficha::find($f->id));
        $this->api->offline=false; $this->api->tablas['/ip/hotspot/user'][0]['disabled']='no';
        $this->delete(route('mt.fichas.eliminar'),$d)->assertRedirect();
        $this->assertNull(Ficha::find($f->id)); $this->assertSame('yes',$this->api->tablas['/ip/hotspot/user'][0]['disabled']);
    }

    public function test_api_decodifica_filas_y_nunca_trata_trap_como_exito(): void
    {
        $filas=RespuestaApi::filas(['!re','=.id=*A','=name=a=b','!re','=.id=*B','=name=Segundo','!done']);
        $this->assertSame([['.id'=>'*A','name'=>'a=b'],['.id'=>'*B','name'=>'Segundo']],$filas);
        $this->expectException(ErrorRouter::class);
        RespuestaApi::filas(['!trap','=message=not enough permissions','!done']);
    }
}




