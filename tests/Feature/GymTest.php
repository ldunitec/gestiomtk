<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Sanctum;
use Modules\Gym\Database\Seeders\GymDatabaseSeeder;
use Modules\Gym\Entities\{Socio, Plan, Membresia, Acceso, Asistencia, Pago};
use Modules\Gym\Services\{AccesoService, MembresiaService};
use Spatie\Permission\Models\{Role, Permission};
use Tests\TestCase;

class GymTest extends TestCase
{
    private ?bool $permitir = true;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2025_03_16_105935_create_permission_tables.php'))->up();
        (require base_path('Modules/Gym/Database/Migrations/2026_11_02_000003_create_gym_tables.php'))->up();
        Gate::before(fn () => $this->permitir);
        Carbon::setTestNow(Carbon::parse('2026-11-02 18:00:00', 'UTC'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-02 18:00:00', 'UTC'));
        $this->withoutExceptionHandling([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function login(): User
    {
        $u = new User(['name' => 'Operador', 'email' => 'gym@example.test']);
        $u->id = 1;
        $this->actingAs($u);
        return $u;
    }

    private function socio(array $d = []): Socio
    {
        return Socio::create(array_merge(['codigo' => 'GYM-'.Str::upper(Str::random(10)), 'nombres' => 'Ana', 'apellidos' => 'Pérez', 'estado' => 'activo'], $d));
    }

    private function plan(array $d = []): Plan
    {
        return Plan::create(array_merge(['nombre' => 'Mensual', 'dias' => 30, 'precio_centavos' => 50000, 'activo' => true], $d));
    }

    private function membresia(Socio $s, array $plan = [], string $pago = '500.00'): Membresia
    {
        return app(MembresiaService::class)->crear($s, $this->plan($plan)->id, '2026-11-02', $pago, 1);
    }

    private function registrar(Socio $s, string $movimiento = 'entrada', ?string $uuid = null): Acceso
    {
        return app(AccesoService::class)->registrar($s->codigo, $movimiento, $uuid ?? (string) Str::uuid(), 1);
    }

    public function test_rutas_requieren_sesion_y_permiso(): void
    {
        $this->getJson(route('gym.index'))->assertUnauthorized();
        $this->postJson(route('gym.api.validar'), ['codigo' => 'X'])->assertUnauthorized();
        $this->login();
        $this->permitir = false;
        foreach (['gym.index', 'gym.socios', 'gym.planes', 'gym.accesos', 'gym.reportes'] as $route) {
            $this->getJson(route($route))->assertForbidden();
        }
        $this->postJson(route('gym.accesos.registrar'), [])->assertForbidden();
        $this->postJson(route('gym.planes.guardar'), [])->assertForbidden();
    }

    public function test_seeder_es_aditivo_e_idempotente(): void
    {
        $r = Role::create(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $p = Permission::create(['name' => 'permiso.existente', 'guard_name' => 'web']);
        $r->givePermissionTo($p);
        $this->seed(GymDatabaseSeeder::class);
        $this->seed(GymDatabaseSeeder::class);
        $this->assertTrue($r->fresh()->hasPermissionTo('permiso.existente'));
        $this->assertTrue($r->fresh()->hasPermissionTo('gym.accesos'));
        $this->assertSame(5, Permission::count());
    }

    public function test_seeder_otorga_acceso_real_al_administrador(): void
    {
        Role::create(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        $this->seed(GymDatabaseSeeder::class);
        $u = $this->login();
        $u->assignRole('ADMINISTRADOR');
        $this->permitir = null;
        $this->get(route('gym.index'))->assertOk();
        $this->get(route('gym.accesos'))->assertOk();
    }

    public function test_paginas_y_formularios_renderizan(): void
    {
        $this->login();
        $s = $this->socio();
        $this->membresia($s, [], '100');
        foreach (['gym.index', 'gym.socios', 'gym.planes', 'gym.accesos', 'gym.reportes'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('Gym');
        }
        $this->get(route('gym.socios.ver', $s))->assertOk()->assertSee($s->codigo)->assertSee('Pendiente de pago');
    }

    public function test_crear_socio_y_plan_desde_panel(): void
    {
        $this->login();
        $this->post(route('gym.socios.guardar'), ['nombres' => 'Carlos', 'apellidos' => 'López', 'estado' => 'activo'])->assertRedirect();
        $s = Socio::firstOrFail();
        $this->assertStringStartsWith('GYM-', $s->codigo);
        $this->post(route('gym.planes.guardar'), ['nombre' => 'Semanal', 'dias' => 7, 'precio' => '199.99', 'limite_visitas' => 5, 'activo' => 1])->assertRedirect();
        $this->assertSame(19999, Plan::firstOrFail()->precio_centavos);
        $this->postJson(route('gym.planes.guardar'), ['nombre' => 'Mal', 'dias' => 0, 'precio' => '-1', 'activo' => 1])->assertUnprocessable();
    }

    public function test_entrada_y_salida_guardan_asistencia_en_utc(): void
    {
        $s = $this->socio();
        $this->membresia($s);
        $a = $this->registrar($s);
        $this->assertTrue($a->permitido);
        $asistencia = Asistencia::firstOrFail();
        $this->assertSame('2026-11-02 18:00:00', $asistencia->getRawOriginal('entrada'));
        $this->assertSame('12:00', $asistencia->entrada->timezone('America/Tegucigalpa')->format('H:i'));
        $this->assertFalse($this->registrar($s)->permitido);
        $this->assertTrue($this->registrar($s, 'salida')->permitido);
        $this->assertSame('cerrada', $asistencia->fresh()->estado);
        $this->assertFalse($this->registrar($s, 'salida')->permitido);
        $this->assertSame(1, Asistencia::count());
    }

    public function test_reintentar_uuid_no_duplica_movimientos_ni_visitas(): void
    {
        $s = $this->socio();
        $this->membresia($s);
        $uuid = (string) Str::uuid();
        $a = $this->registrar($s, 'entrada', $uuid);
        $b = $this->registrar($s, 'entrada', $uuid);
        $this->assertSame($a->id, $b->id);
        $this->assertSame(1, Asistencia::count());
        $this->assertSame(1, Acceso::count());
        $this->expectException(ValidationException::class);
        $this->registrar($s, 'salida', $uuid);
    }

    public function test_rechaza_estado_pago_vencimiento_y_desconocidos(): void
    {
        $service = app(AccesoService::class);
        $s = $this->socio();
        $this->assertFalse($this->registrar($s)->permitido);
        $m = $this->membresia($s, [], '0');
        $this->assertSame('Membresía pendiente de pago', $this->registrar($s)->motivo);
        app(MembresiaService::class)->pagar($m, '500', null, 1);
        $s->update(['estado' => 'suspendido']);
        $this->assertSame('Socio suspendido', $this->registrar($s)->motivo);
        $s->update(['estado' => 'activo']);
        $m->update(['inicio' => '2026-10-01', 'fin' => '2026-10-31']);
        $this->assertFalse($this->registrar($s)->permitido);
        $this->assertFalse($service->registrar('NO-EXISTE', 'entrada', (string) Str::uuid(), 1)->permitido);
        $this->assertSame(0, Asistencia::count());
        $this->assertSame(5, Acceso::count());
    }

    public function test_vigencia_inclusiva_y_cambio_de_fecha_en_honduras(): void
    {
        $s = $this->socio();
        $m = $this->membresia($s, ['dias' => 1]);
        $this->assertSame('2026-11-02', $m->fin);
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-03 05:59:59', 'UTC'));
        $this->assertTrue(app(AccesoService::class)->evaluar($s)['permitido']);
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-03 06:00:00', 'UTC'));
        $this->assertFalse(app(AccesoService::class)->evaluar($s)['permitido']);
    }

    public function test_no_permite_entrada_antes_de_inicio(): void
    {
        $s = $this->socio();
        app(MembresiaService::class)->crear($s, $this->plan()->id, '2026-11-03', '500', 1);
        $this->assertFalse($this->registrar($s)->permitido);
    }

    public function test_limite_visitas_se_consume_solo_en_entrada_permitida(): void
    {
        $s = $this->socio();
        $this->membresia($s, ['limite_visitas' => 1]);
        $this->assertTrue($this->registrar($s)->permitido);
        $this->assertTrue($this->registrar($s, 'salida')->permitido);
        $this->assertSame('Visitas del plan agotadas', $this->registrar($s)->motivo);
        $this->assertSame(1, Asistencia::count());
    }

    public function test_salida_no_se_bloquea_por_suspension_o_vencimiento(): void
    {
        $s = $this->socio();
        $m = $this->membresia($s);
        $this->registrar($s);
        $s->update(['estado' => 'suspendido']);
        $m->update(['anulada' => true]);
        $this->assertTrue($this->registrar($s, 'salida')->permitido);
    }

    public function test_entrada_abierta_del_dia_anterior_queda_sin_salida(): void
    {
        $s = $this->socio();
        $this->membresia($s);
        $this->registrar($s);
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-03 18:00:00', 'UTC'));
        $this->assertTrue($this->registrar($s)->permitido);
        $this->assertSame('sin_salida', Asistencia::orderBy('id')->first()->estado);
        $this->assertNull(Asistencia::orderBy('id')->first()->salida);
    }

    public function test_precio_y_limite_del_plan_se_conservan_en_membresia(): void
    {
        $s = $this->socio();
        $p = $this->plan(['limite_visitas' => 2]);
        $m = app(MembresiaService::class)->crear($s, $p->id, '2026-11-02', '500', 1);
        $p->update(['precio_centavos' => 90000, 'limite_visitas' => 1, 'activo' => false]);
        $this->assertSame(50000, $m->fresh()->precio_centavos);
        $this->assertSame(2, $m->fresh()->limite_visitas);
        $this->assertTrue($this->registrar($s)->permitido);
    }

    public function test_no_se_vende_plan_inactivo(): void
    {
        $this->expectException(ValidationException::class);
        $this->membresia($this->socio(), ['activo' => false]);
    }

    public function test_no_permite_membresias_superpuestas(): void
    {
        $s = $this->socio();
        $m = $this->membresia($s);
        $this->assertSame('2026-12-01', $m->fin);
        $this->expectException(ValidationException::class);
        app(MembresiaService::class)->crear($s, $this->plan()->id, $m->fin, '500', 1);
    }

    public function test_renovacion_posterior_y_anulacion_conservan_pagos(): void
    {
        $this->login();
        $s = $this->socio();
        $m = $this->membresia($s);
        $nuevo = app(MembresiaService::class)->crear($s, $m->plan_id, '2026-12-02', '500', 1);
        $this->post(route('gym.anular', $m))->assertRedirect();
        $this->assertTrue($m->fresh()->anulada);
        $this->assertSame(2, Pago::count());
        $this->assertFalse($nuevo->anulada);
    }

    public function test_abonos_exactos_y_sobrepago_rechazado(): void
    {
        $s = $this->socio();
        $m = $this->membresia($s, ['precio_centavos' => 10010], '50.05');
        $this->assertFalse($this->registrar($s)->permitido);
        app(MembresiaService::class)->pagar($m, '50.05', 'REC-1', 1);
        $this->assertSame(10010, $m->fresh()->pagado_centavos);
        $this->assertSame(10010, (int) Pago::sum('centavos'));
        $this->assertTrue($this->registrar($s)->permitido);
        $this->expectException(ValidationException::class);
        app(MembresiaService::class)->pagar($m, '0.01', null, 1);
    }

    public function test_plan_gratuito_y_montos_invalidos(): void
    {
        $s = $this->socio();
        $this->membresia($s, ['precio_centavos' => 0], '0');
        $this->assertTrue($this->registrar($s)->permitido);
        $this->expectException(ValidationException::class);
        MembresiaService::centavos('1.999');
    }

    public function test_api_exige_capacidad_y_validar_no_registra_asistencia(): void
    {
        $u = $this->login();
        $s = $this->socio();
        $this->membresia($s);
        Sanctum::actingAs($u, []);
        $this->postJson(route('gym.api.validar'), ['codigo' => $s->codigo])->assertForbidden();
        Sanctum::actingAs($u, ['gym:acceso']);
        $this->postJson(route('gym.api.validar'), ['codigo' => $s->codigo])->assertOk()->assertJson(['permitido' => true]);
        $this->assertSame(0, Acceso::count());
        $this->assertSame(0, Asistencia::count());
        $d = ['codigo' => $s->codigo, 'movimiento' => 'entrada', 'solicitud_id' => (string) Str::uuid()];
        $this->postJson(route('gym.api.accesos'), $d)->assertOk()->assertJson(['permitido' => true]);
        $this->postJson(route('gym.api.accesos'), $d)->assertOk()->assertJson(['permitido' => true]);
        $this->assertSame(1, Asistencia::count());
        $this->postJson(route('gym.api.accesos'), ['codigo' => $s->codigo, 'movimiento' => 'entrada'])->assertUnprocessable();
    }

    public function test_reporte_filtra_fechas_en_honduras_y_csv_escapa_formulas(): void
    {
        $this->login();
        $s = $this->socio(['nombres' => '=CMD']);
        $this->membresia($s);
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-03 05:00:00', 'UTC'));
        $this->registrar($s);
        $q = ['desde' => '2026-11-02', 'hasta' => '2026-11-02', 'codigo' => $s->codigo];
        $this->get(route('gym.reportes', $q))->assertOk()->assertViewHas('total', 1);
        $q['csv'] = 1;
        $csv = $this->get(route('gym.reportes', $q))->assertOk()->streamedContent();
        $this->assertStringContainsString("'=CMD", $csv);
        $this->assertStringContainsString('2026-11-02 23:00:00', $csv);
        $this->get(route('gym.reportes', ['desde' => '2026-11-03', 'hasta' => '2026-11-03']))->assertOk()->assertViewHas('total', 0);
        $this->getJson(route('gym.reportes', ['desde' => '2020-01-01', 'hasta' => '2026-11-02']))->assertUnprocessable();
    }
}
