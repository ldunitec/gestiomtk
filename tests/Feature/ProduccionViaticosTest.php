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
        (require database_path('migrations/2026_02_05_112230_create_ciudades_table.php'))->up();
        (require database_path('migrations/2026_02_05_113521_create_viaticos_table.php'))->up();
        (require database_path('migrations/2026_11_02_000001_add_produccion_fields_to_viaticos.php'))->up();
        Gate::before(fn () => $this->permitir);
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
        ]))->assertRedirect();
        $viatico = Viatico::firstOrFail();
        $this->assertSame('100.25', $viatico->distancia_km);
        $this->assertSame('Ciudad de prueba', $viatico->ciudad);
        $this->assertSame('1750.50', $viatico->mano_obra);
        $this->assertEquals(828, $viatico->combustible);
        $this->assertSame('6422.50', $viatico->total);
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
        $pdf = $this->get(route('admin.viaticos.pdf', $viatico->id));
        $pdf->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $this->assertStringContainsString('/produccion/admin/viaticos', route('admin.viaticos.create'));
        $this->assertStringEndsWith('/'.$viatico->id.'/pdf', route('admin.viaticos.pdf', $viatico->id));
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
            $this->assertEqualsWithDelta($combustibleAnterior + 2000 + 44 + 1300 + 500, $resultado['total'], 0.001);
        }
    }
}
