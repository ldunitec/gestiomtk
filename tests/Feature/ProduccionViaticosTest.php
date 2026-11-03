<?php

namespace Tests\Feature;

use App\Models\Ciudad;
use App\Models\User;
use App\Models\Viatico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Modules\Produccion\Services\ViaticoCalculator;
use Tests\TestCase;

class ProduccionViaticosTest extends TestCase
{
    protected bool $permitir = true;

    protected function setUp(): void
    {
        parent::setUp();
        // Pruebas aisladas: nunca ejecutar migraciones sobre la base configurada del servidor.
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        (require database_path('migrations/2025_03_16_105935_create_permission_tables.php'))->up();
        (require database_path('migrations/2026_02_05_112230_create_ciudades_table.php'))->up();
        (require database_path('migrations/2026_02_05_113521_create_viaticos_table.php'))->up();
        (require database_path('migrations/2026_11_02_000001_add_produccion_fields_to_viaticos.php'))->up();
        (require database_path('migrations/2026_11_03_000002_add_impuesto_to_viaticos.php'))->up();
        Gate::before(fn () => $this->permitir);
        $this->withoutExceptionHandling([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);
    }

    protected function login(): void
    {
        $user = new User(['name' => 'Prueba', 'email' => 'prueba@example.test']);
        $user->id = 1;
        $this->actingAs($user);
    }

    protected function ciudad(array $overrides = []): Ciudad
    {
        return Ciudad::create(array_merge([
            'nombre' => 'Ciudad de prueba', 'cliente' => '',
            'km_desde_tgu' => 100.25, 'km_desde_sps' => 0,
        ], $overrides));
    }

    protected function payload(Ciudad $ciudad, array $overrides = []): array
    {
        return array_merge([
            'ciudad_id' => $ciudad->id, 'origen' => 'Tegucigalpa',
            'precio_combustible' => 120, 'precio_peaje' => 22, 'precio_hotel' => 1300,
            'precio_alimentacion' => 500, 'extra' => 500, 'mano_obra' => '1750.50',
            'dias' => 2, 'noches' => 1, 'peajes' => 2, 'cantidad_tecnicos' => 2,
        ], $overrides);
    }

    public function test_requiere_sesion_y_permiso(): void
    {
        $this->getJson(route('viaticos.list'))->assertUnauthorized();
        $this->login();
        $this->permitir = false;
        $this->getJson(route('viaticos.list'))->assertForbidden();
        $this->postJson(route('produccion.admin.viaticos.ciudades.store'), [])->assertForbidden();
    }

    public function test_guarda_mano_de_obra_y_distancia_del_catalogo_sin_duplicar_el_regreso(): void
    {
        $this->login();
        $ciudad = $this->ciudad();
        $this->post(route('admin.viaticos.store'), $this->payload($ciudad, [
            'distancia_km' => 999999, 'ciudad' => 'Nombre manipulado',
            'subtotal' => 1, 'impuesto_porcentaje' => 0, 'impuesto' => 0, 'total' => 1,
        ]))->assertRedirect();
        $viatico = Viatico::firstOrFail();
        $this->assertSame('100.25', $viatico->distancia_km);
        $this->assertSame('Ciudad de prueba', $viatico->ciudad);
        $this->assertSame('1750.50', $viatico->mano_obra);
        $this->assertEquals(828, $viatico->combustible);
        $this->assertSame('6422.50', $viatico->subtotal);
        $this->assertSame('15.00', $viatico->impuesto_porcentaje);
        $this->assertSame('963.38', $viatico->impuesto);
        $this->assertSame('7385.88', $viatico->total);
        $this->assertEquals(1, $viatico->noches);
        $ciudad->update(['nombre' => 'Nombre cambiado', 'km_desde_tgu' => 900]);
        $this->assertSame('100.25', $viatico->fresh()->distancia_km);
        $this->assertSame('Ciudad de prueba', $viatico->fresh()->ciudad);
    }

    public function test_seleccion_sps_acepta_cero_y_ruta_sin_kilometros_se_rechaza(): void
    {
        $this->login();
        $ciudad = $this->ciudad(['km_desde_tgu' => null]);
        $this->postJson(route('admin.viaticos.store'), $this->payload($ciudad))
            ->assertUnprocessable()->assertJsonValidationErrors('ciudad_id');
        $this->post(route('admin.viaticos.store'), $this->payload($ciudad, ['origen' => 'San Pedro Sula']))
            ->assertRedirect();
        $this->assertSame('0.00', Viatico::firstOrFail()->distancia_km);
    }

    public function test_rechaza_mano_de_obra_negativa_y_origen_no_permitido(): void
    {
        $this->login();
        $this->postJson(route('admin.viaticos.store'), $this->payload($this->ciudad(), [
            'mano_obra' => -1, 'origen' => 'Otro',
        ]))->assertUnprocessable()->assertJsonValidationErrors(['mano_obra', 'origen']);
        $this->assertSame(0, Viatico::count());
    }

    public function test_catalogo_crea_edita_y_distingue_cero_de_sin_configurar(): void
    {
        $this->login();
        $ruta = route('produccion.admin.viaticos.ciudades.store');
        $this->postJson($ruta, ['nombre' => 'Destino'])->assertUnprocessable();
        $this->post($ruta, ['nombre' => 'Destino', 'km_desde_tgu' => 0, 'km_desde_sps' => 250.75])
            ->assertRedirect();
        $ciudad = Ciudad::firstOrFail();
        $this->assertSame('0.00', $ciudad->km_desde_tgu);
        $this->put(route('produccion.admin.viaticos.ciudades.update', $ciudad->id), [
            'nombre' => 'Destino', 'km_desde_tgu' => 12.5, 'km_desde_sps' => '',
        ])->assertRedirect();
        $this->assertSame('12.50', $ciudad->fresh()->km_desde_tgu);
        $this->assertNull($ciudad->fresh()->km_desde_sps);
        $this->get(route('admin.viaticos.ciudades'))->assertOk()->assertSee('12.50');
    }

    public function test_pantallas_pdf_y_enlaces_estan_en_produccion(): void
    {
        $this->login();
        $this->get(route('admin.viaticos.create'))->assertOk()->assertSee('Distancia de ida');
        $this->post(route('admin.viaticos.store'), $this->payload($this->ciudad()))->assertRedirect();
        $viatico = Viatico::firstOrFail();
        $this->get(route('admin.viaticos.index'))->assertOk();
        $this->get(route('admin.viaticos.show', $viatico->id))->assertOk()->assertSee('1,750.50');
        $html = view('produccion::admin.viaticos.pdf', compact('viatico'))->render();
        $this->assertStringContainsString('1,750.50', $html);
        $this->assertStringContainsString('6,422.50', $html);
        $this->assertStringContainsString('IMPUESTO (15%)', $html);
        $this->assertStringContainsString('963.38', $html);
        $this->assertStringContainsString('7,385.88', $html);
        $this->get(route('admin.viaticos.show', $viatico->id))->assertOk()
            ->assertSee('Impuesto (15%)')->assertSee('963.38')->assertSee('7,385.88');
        $this->getJson(route('viaticos.list'))->assertOk()->assertJsonPath('data.0.subtotal', '6422.50')
            ->assertJsonPath('data.0.impuesto', '963.38')->assertJsonPath('data.0.total', '7385.88');
        $pdf = $this->get(route('admin.viaticos.pdf', $viatico->id));
        $pdf->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $documento = \Barryvdh\DomPDF\Facade\Pdf::loadView('produccion::admin.viaticos.pdf', compact('viatico'))
            ->setPaper('letter', 'portrait');
        $documento->output();
        $this->assertSame(1, $documento->getDomPDF()->getCanvas()->get_page_count(), 'El reporte con impuesto debe caber en una página.');
        $this->assertStringContainsString('/produccion/admin/viaticos', route('admin.viaticos.create'));
        $this->assertStringEndsWith('/'.$viatico->id.'/pdf', route('admin.viaticos.pdf', $viatico->id));
    }

    public function test_crea_ciudad_desde_el_viatico_y_reutiliza_su_kilometraje(): void
    {
        $this->login();
        $payload = $this->payload($this->ciudad(), [
            'ciudad_id' => 'nueva', 'nueva_ciudad' => '  Nueva   ciudad  ',
            'km_nueva_ciudad' => '145.75',
        ]);
        $this->post(route('admin.viaticos.store'), $payload)->assertRedirect();
        $ciudad = Ciudad::where('nombre', 'Nueva ciudad')->firstOrFail();
        $this->assertSame('145.75', $ciudad->km_desde_tgu);
        $this->assertNull($ciudad->km_desde_sps);
        $viatico = Viatico::firstOrFail();
        $this->assertSame($ciudad->id, $viatico->ciudad_id);
        $this->assertSame('145.75', $viatico->distancia_km);
        $this->get(route('admin.viaticos.create'))->assertOk()->assertSee('Nueva ciudad');
        $this->post(route('admin.viaticos.store'), $this->payload($ciudad, [
            'distancia_km' => 99999, 'nueva_ciudad' => 'Ignorar', 'km_nueva_ciudad' => 99999,
        ]))->assertRedirect();
        $this->assertSame(2, Ciudad::count());
        $this->assertSame('145.75', Viatico::orderByDesc('id')->firstOrFail()->distancia_km);
    }

    public function test_ciudad_nueva_desde_sps_guarda_cero_y_no_inventa_distancia_tgu(): void
    {
        $this->login();
        $this->post(route('admin.viaticos.store'), $this->payload($this->ciudad(), [
            'ciudad_id' => 'nueva', 'origen' => 'San Pedro Sula',
            'nueva_ciudad' => 'Destino local', 'km_nueva_ciudad' => 0,
        ]))->assertRedirect();
        $ciudad = Ciudad::where('nombre', 'Destino local')->firstOrFail();
        $this->assertSame('0.00', $ciudad->km_desde_sps);
        $this->assertNull($ciudad->km_desde_tgu);
    }

    public function test_rechaza_ciudad_nueva_sin_nombre_km_invalido_o_nombre_ya_registrado(): void
    {
        $this->login();
        $ciudad = $this->ciudad();
        $this->postJson(route('admin.viaticos.store'), $this->payload($ciudad, [
            'ciudad_id' => 'nueva', 'nueva_ciudad' => '', 'km_nueva_ciudad' => -1,
        ]))->assertUnprocessable()->assertJsonValidationErrors(['nueva_ciudad', 'km_nueva_ciudad']);
        $this->postJson(route('admin.viaticos.store'), $this->payload($ciudad, [
            'ciudad_id' => 'nueva', 'nueva_ciudad' => 'CIUDAD DE PRUEBA', 'km_nueva_ciudad' => 10,
        ]))->assertUnprocessable()->assertJsonValidationErrors('nueva_ciudad');
        $this->assertSame(1, Ciudad::count());
        $this->assertSame(0, Viatico::count());
    }

    public function test_no_guarda_ciudad_si_el_viatico_no_puede_guardarse(): void
    {
        $this->login();
        $this->postJson(route('admin.viaticos.store'), $this->payload($this->ciudad(), [
            'ciudad_id' => 'nueva', 'nueva_ciudad' => 'No guardar', 'km_nueva_ciudad' => 10,
            'precio_alimentacion' => 1000000, 'cantidad_tecnicos' => 1000, 'dias' => 365,
        ]))->assertUnprocessable();
        $this->assertFalse(Ciudad::where('nombre', 'No guardar')->exists());
        $this->assertSame(0, Viatico::count());
    }

    public function test_migracion_preserva_historicos_y_formula_anterior_con_mano_de_obra_cero(): void
    {
        $migration = require database_path('migrations/2026_11_02_000001_add_produccion_fields_to_viaticos.php');
        $migration->down();
        $id = DB::table('viaticos')->insertGetId([
            'ciudad' => 'Destino histórico', 'distancia_km' => 200,
            'hospedaje' => 1300, 'alimentacion' => 500, 'peajes' => 22,
            'combustible' => 1518, 'cantidad_tecnicos' => 1, 'dias' => 1,
            'total' => 3340,
        ]);
        $migration->up();
        $historico = Viatico::findOrFail($id);
        $this->assertSame('3340.00', $historico->total);
        $this->assertSame('0.00', $historico->mano_obra);
        $this->assertNull($historico->origen);
        $this->assertSame('Destino histórico', $historico->ciudad);
        $this->assertTrue(Schema::hasColumns('viaticos', ['origen', 'ciudad_id', 'mano_obra', 'noches', 'extra']));
        $ciudad = $this->ciudad();
        $calculator = new ViaticoCalculator();
        foreach ([0, 5, 40, 100.25, 250, 1000] as $km) {
            $data = $this->payload($ciudad, ['distancia_km' => $km, 'mano_obra' => 0]);
            $resultado = $calculator->calculate($data);
            $combustibleAnterior = ceil(($km * 2 + 30) / 40) * 120 * 1.15;
            $this->assertEqualsWithDelta($combustibleAnterior, $resultado['combustible'], 0.001);
            $this->assertEqualsWithDelta($combustibleAnterior + 2000 + 44 + 1300 + 500, $resultado['subtotal'], 0.001);
            $this->assertEqualsWithDelta(round($resultado['subtotal'] * 0.15, 2), $resultado['impuesto'], 0.001);
            $this->assertEqualsWithDelta($resultado['subtotal'] + $resultado['impuesto'], $resultado['total'], 0.001);
        }
    }

    public function test_impuesto_redondea_a_centavos_y_admite_subtotal_cero(): void
    {
        $calculator = new ViaticoCalculator();
        foreach ([['0.00', 0, 0], ['0.10', 0.02, 0.12], ['0.30', 0.05, 0.35], ['1.10', 0.17, 1.27], ['1000.00', 150, 1150]] as [$manoObra, $impuesto, $total]) {
            $resultado = $calculator->calculate([
                'distancia_km' => 0, 'precio_combustible' => 0, 'cantidad_tecnicos' => 1,
                'dias' => 1, 'precio_alimentacion' => 0, 'precio_peaje' => 0, 'peajes' => 0,
                'precio_hotel' => 0, 'noches' => 0, 'extra' => 0, 'mano_obra' => $manoObra,
            ]);
            $this->assertEquals((float) $manoObra, $resultado['subtotal']);
            $this->assertEquals($impuesto, $resultado['impuesto']);
            $this->assertEquals($total, $resultado['total']);
        }
    }

    public function test_limite_del_total_incluye_el_impuesto(): void
    {
        $this->login();
        $this->postJson(route('admin.viaticos.store'), $this->payload($this->ciudad(), [
            'precio_combustible' => 0, 'precio_peaje' => 0, 'precio_hotel' => 0, 'extra' => 0,
            'mano_obra' => 0, 'dias' => 1, 'noches' => 0, 'peajes' => 0,
            'precio_alimentacion' => 100000, 'cantidad_tecnicos' => 900,
        ]))->assertUnprocessable()->assertJsonValidationErrors('mano_obra');
        $this->assertSame(0, Viatico::count());
    }

    public function test_migracion_de_impuesto_preserva_totales_y_pdf_historicos(): void
    {
        $this->login();
        $migration = require database_path('migrations/2026_11_03_000002_add_impuesto_to_viaticos.php');
        $migration->down();
        $id = DB::table('viaticos')->insertGetId([
            'ciudad' => 'Viaje anterior al impuesto', 'distancia_km' => 0, 'hospedaje' => 0,
            'alimentacion' => 500, 'peajes' => 0, 'combustible' => 500,
            'cantidad_tecnicos' => 1, 'dias' => 1, 'total' => 1000,
        ]);
        $migration->up();
        $viatico = Viatico::findOrFail($id);
        $this->assertSame('1000.00', $viatico->total);
        $this->assertNull($viatico->subtotal);
        $this->assertNull($viatico->impuesto_porcentaje);
        $this->assertNull($viatico->impuesto);
        $html = view('produccion::admin.viaticos.pdf', compact('viatico'))->render();
        $this->assertStringContainsString('IMPUESTO NO REGISTRADO (VIÁTICO ANTERIOR)', $html);
        $this->assertStringContainsString('1,000.00', $html);
        $this->assertStringNotContainsString('1,150.00', $html);
        $this->get(route('admin.viaticos.show', $id))->assertOk()->assertSee('Impuesto no registrado');
        $this->get(route('admin.viaticos.pdf', $id))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }
}
