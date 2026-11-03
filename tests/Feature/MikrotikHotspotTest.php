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
        if ($publicar) { $s->publicar($l, 1); }
        return $l;
    }

    public function test_rutas_y_escrituras_requieren_sesion_y_permisos(): void
    {
        $this->getJson(route('mt.index'))->assertUnauthorized();
        $this->postJson(route('mt.crear'), [])->assertUnauthorized();
        $this->login(); $this->permitir = false;
        foreach (['mt.index','mt.configuracion','mt.fichas'] as $route) { $this->getJson(route($route))->assertForbidden(); }
        $this->postJson(route('mt.guardar'), [])->assertForbidden();
        $this->postJson(route('mt.crear'), [])->assertForbidden();
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
        $this->assertCount(2,$this->api->tablas['/ip/hotspot/user/profile']);
        $this->assertSame('CODIGO ORIGINAL',$this->api->tablas['/ip/hotspot/user/profile'][1]['on-login']);
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

    public function test_api_decodifica_filas_y_nunca_trata_trap_como_exito(): void
    {
        $filas=RespuestaApi::filas(['!re','=.id=*A','=name=a=b','!re','=.id=*B','=name=Segundo','!done']);
        $this->assertSame([['.id'=>'*A','name'=>'a=b'],['.id'=>'*B','name'=>'Segundo']],$filas);
        $this->expectException(ErrorRouter::class);
        RespuestaApi::filas(['!trap','=message=not enough permissions','!done']);
    }
}
