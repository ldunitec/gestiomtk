<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Impresion\Entities\Crey;
use Modules\Impresion\Imports\ImpresionesImport;
use Modules\Impresion\Services\FotoService;
use Tests\TestCase;
use ZipArchive;

class PanelImpresionTest extends TestCase
{
    protected bool $permitir = true;
    protected array $archivos = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'impresion.fotos_origen' => null, 'impresion.max_zip_bytes' => 104857600]);
        DB::purge('sqlite');
        (require database_path('migrations/2025_03_16_105935_create_permission_tables.php'))->up();
        (require database_path('migrations/2026_11_02_000002_add_control_impresion_to_creys.php'))->up();
        Gate::before(fn () => $this->permitir);
        $this->withoutExceptionHandling([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ]);
    }

    protected function tearDown(): void
    {
        foreach (Crey::whereNotNull('foto_path')->pluck('foto_path') as $path) {
            $this->archivos[] = storage_path('app/impresion/'.$path);
        }
        foreach ($this->archivos as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        parent::tearDown();
    }

    protected function login(): void
    {
        $user = new User(['name' => 'Prueba', 'email' => 'prueba@example.test']);
        $user->id = 1;
        $this->actingAs($user);
    }

    protected function alumno(bool $conFoto = true, array $datos = []): Crey
    {
        $alumno = Crey::create(array_merge([
            'curso' => 'SEPTIMO', 'seccion' => 'A', 'modalidad' => 'GENERAL',
            'dni' => (string) Str::uuid(), 'nombres' => 'Juan', 'apellidos' => 'Pérez López',
            'observaciones' => 'Pendiente',
        ], $datos));
        if ($conFoto) {
            $relative = 'fotos/'.Str::uuid().'.png';
            $path = storage_path('app/impresion/'.$relative);
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }
            file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l9sAAAAASUVORK5CYII='));
            $alumno->foto_path = $relative;
            $alumno->save();
        }

        return $alumno;
    }

    protected function seleccion(array $ids, array $opciones = []): array
    {
        return array_merge(['alcance' => 'seleccion', 'ids' => $ids, 'estado' => 'todos', 'marcar_impresos' => 1], $opciones);
    }

    protected function abrirZip($respuesta): ZipArchive
    {
        $respuesta->assertOk();
        $path = $respuesta->baseResponse->getFile()->getPathname();
        $this->archivos[] = $path;
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path));

        return $zip;
    }

    public function test_panel_y_fotos_requieren_sesion_y_permiso(): void
    {
        $alumno = $this->alumno();
        $this->getJson(route('impresion.panel'))->assertUnauthorized();
        $this->getJson(route('impresion.foto', $alumno->id))->assertUnauthorized();
        $this->login();
        $this->permitir = false;
        $this->getJson(route('impresion.panel'))->assertForbidden();
        $this->postJson(route('impresion.zip'), $this->seleccion([$alumno->id]))->assertForbidden();
    }

    public function test_panel_no_confunde_foto_registrada_con_impreso_y_filtra_seccion(): void
    {
        $this->login();
        $this->alumno(true, ['observaciones' => 'foto123']);
        $this->alumno(false, ['seccion' => 'B']);
        $this->get(route('impresion.panel', ['curso' => 'SEPTIMO', 'seccion' => 'A']))
            ->assertOk()->assertViewHas('total', 1)->assertViewHas('impresos', 0)
            ->assertSee('JUAN PÉREZ LÓPEZ')->assertSee('Pendiente');
    }

    public function test_zip_por_seleccion_usa_mayusculas_no_sobrescribe_homonimos_y_marca_solo_fotos(): void
    {
        $this->login();
        $uno = $this->alumno();
        $dos = $this->alumno();
        $sinFoto = $this->alumno(false);
        $response = $this->post(route('impresion.zip'), $this->seleccion([$uno->id, $dos->id, $sinFoto->id]));
        $zip = $this->abrirZip($response);
        $this->assertNotFalse($zip->getFromName('SEPTIMO/A/GENERAL/JUAN PÉREZ LÓPEZ.png'));
        $this->assertNotFalse($zip->getFromName('SEPTIMO/A/GENERAL/JUAN PÉREZ LÓPEZ - ID '.$dos->id.'.png'));
        $this->assertStringContainsString('SIN FOTO', $zip->getFromName('RESUMEN.csv'));
        $zip->close();
        $this->assertNotNull($uno->fresh()->impreso_at);
        $this->assertEquals(1, $uno->fresh()->impreso_por);
        $this->assertNotNull($dos->fresh()->impreso_at);
        $this->assertNull($sinFoto->fresh()->impreso_at);
        $this->assertEquals(2, DB::table('impresion_lotes')->first()->marcados);
    }

    public function test_zip_de_seccion_incluye_todas_las_paginas_y_respeta_modalidad(): void
    {
        $this->login();
        $this->alumno();
        for ($i = 0; $i < 50; $i++) {
            $this->alumno();
        }
        $otro = $this->alumno(true, ['seccion' => 'B']);
        $otraModalidad = $this->alumno(true, ['modalidad' => 'OTRA']);
        $zip = $this->abrirZip($this->post(route('impresion.zip'), [
            'alcance' => 'seccion', 'curso' => 'SEPTIMO', 'seccion' => 'A',
            'modalidad' => 'GENERAL', 'estado' => 'todos', 'marcar_impresos' => 1,
        ]));
        $this->assertEquals(52, $zip->numFiles);
        $zip->close();
        $this->assertEquals(51, Crey::whereNotNull('impreso_at')->count());
        $this->assertNull($otro->fresh()->impreso_at);
        $this->assertNull($otraModalidad->fresh()->impreso_at);
    }

    public function test_zip_puede_exportarse_sin_marcar_impresos(): void
    {
        $this->login();
        $alumno = $this->alumno();
        $zip = $this->abrirZip($this->post(route('impresion.zip'), $this->seleccion([$alumno->id], ['marcar_impresos' => 0])));
        $zip->close();
        $this->assertNull($alumno->fresh()->impreso_at);
        $this->assertEquals(0, DB::table('impresion_lotes')->first()->marcados);
    }

    public function test_lote_sin_fotos_o_demasiado_grande_no_marca_ni_registra_exito(): void
    {
        $this->login();
        $alumno = $this->alumno(false);
        $this->postJson(route('impresion.zip'), $this->seleccion([$alumno->id]))->assertUnprocessable();
        $conFoto = $this->alumno();
        config(['impresion.max_zip_bytes' => 1]);
        $this->postJson(route('impresion.zip'), $this->seleccion([$conFoto->id]))->assertUnprocessable();
        $this->assertSame(0, DB::table('impresion_lotes')->count());
        $this->assertSame(0, Crey::whereNotNull('impreso_at')->count());
    }

    public function test_seccion_requiere_curso_y_seccion_y_seleccion_no_acepta_ids_ajenos_al_filtro(): void
    {
        $this->login();
        $alumno = $this->alumno();
        $this->postJson(route('impresion.zip'), ['alcance' => 'seccion'])->assertUnprocessable();
        $this->postJson(route('impresion.zip'), $this->seleccion([$alumno->id], ['seccion' => 'B']))->assertUnprocessable();
        $this->postJson(route('impresion.zip'), $this->seleccion([$alumno->id, $alumno->id]))->assertUnprocessable();
        $this->assertNull($alumno->fresh()->impreso_at);
    }

    public function test_vista_previa_no_marca_y_confirmacion_registra_impresion_una_sola_vez(): void
    {
        $this->login();
        $alumno = $this->alumno();
        $response = $this->post(route('impresion.preparar'), $this->seleccion([$alumno->id]));
        $response->assertRedirect();
        $url = $response->headers->get('Location');
        $token = basename($url);
        $this->get($url)->assertOk()->assertSee('Cancelar el diálogo')->assertSee('JUAN PÉREZ LÓPEZ');
        $this->assertNull($alumno->fresh()->impreso_at);
        $this->post(route('impresion.confirmar', $token))->assertRedirect(route('impresion.panel'));
        $this->assertNotNull($alumno->fresh()->impreso_at);
        $this->postJson(route('impresion.confirmar', $token))->assertNotFound();
        $this->assertEquals(1, DB::table('impresion_lotes')->count());
    }

    public function test_foto_cambiada_o_vista_de_otra_sesion_no_puede_confirmarse(): void
    {
        $this->login();
        $alumno = $this->alumno();
        $response = $this->post(route('impresion.preparar'), $this->seleccion([$alumno->id]));
        $token = basename($response->headers->get('Location'));
        $alumno->nombres = 'Cambio después de preparar';
        $alumno->save();
        $this->postJson(route('impresion.confirmar', $token))->assertUnprocessable();
        $this->assertNull($alumno->fresh()->impreso_at);
        session()->forget('impresion.previas');
        $this->getJson(route('impresion.vista', $token))->assertNotFound();
    }

    public function test_subir_foto_reemplaza_archivo_y_deja_pendiente(): void
    {
        $this->login();
        $alumno = $this->alumno();
        $this->archivos[] = storage_path('app/impresion/'.$alumno->foto_path);
        $alumno->impreso_at = now();
        $alumno->save();
        $this->post(route('impresion.foto.upload', $alumno->id), [
            'foto' => UploadedFile::fake()->image('nueva.jpg', 100, 140),
        ])->assertRedirect();
        $this->assertNull($alumno->fresh()->impreso_at);
        $this->assertStringEndsWith('.jpg', $alumno->fresh()->foto_path);
        $this->get(route('impresion.foto', $alumno->id))->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->postJson(route('impresion.foto.upload', $alumno->id), [
            'foto' => UploadedFile::fake()->create('archivo.php', 1, 'text/plain'),
        ])->assertUnprocessable();
    }

    public function test_resolver_rechaza_rutas_fuera_de_carpeta_e_imagenes_falsas(): void
    {
        $alumno = $this->alumno(false, ['observaciones' => '../../.env']);
        $fotos = app(FotoService::class);
        $this->assertNull($fotos->resolve($alumno));
        $alumno->foto_path = '../../.env';
        $this->assertNull($fotos->resolve($alumno));
        $path = storage_path('app/impresion/fotos/'.Str::uuid().'.jpg');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }
        file_put_contents($path, 'Esto no es una imagen');
        $this->archivos[] = $path;
        $alumno->foto_path = 'fotos/'.basename($path);
        $this->assertNull($fotos->resolve($alumno));
    }

    public function test_importar_sin_fotos_no_interrumpe_ni_duplica_y_preserva_estado_si_no_hay_cambios(): void
    {
        $import = new ImpresionesImport();
        $data = ['dni' => '123', 'nombre' => 'JUAN PEREZ LOPEZ', 'curso' => 'SEPTIMO',
            'seccion' => 'A', 'modalidad' => 'GENERAL', 'foto' => 'no_existe'];
        $alumno = $import->model($data);
        $alumno->save();
        $alumno->impreso_at = now();
        $alumno->save();
        $import->model($data)->save();
        $this->assertEquals(1, Crey::count());
        $this->assertNotNull($alumno->fresh()->impreso_at);
        $this->assertNull(app(FotoService::class)->resolve($alumno->fresh()));
        $import->model(array_merge($data, ['curso' => 'OCTAVO']))->save();
        $this->assertNull($alumno->fresh()->impreso_at);
    }

    public function test_nombres_archivo_y_tabla_correcta(): void
    {
        $service = app(FotoService::class);
        $this->assertSame('JUAN PÉREZ LÓPEZ', $service->safeName('Juan Pérez López'));
        $this->assertSame('NOMBRE MALICIOSO', $service->safeName('../nombre/malicioso'));
        $this->assertSame('_CON', $service->safeName('CON'));
        $this->assertTrue(Schema::hasColumns('creys', ['id', 'curso', 'foto_path', 'impreso_at', 'impreso_por']));
        $this->assertSame('creys', (new Crey())->getTable());
    }

    public function test_reconoce_fotos_heredadas_por_carpeta_y_por_codigo(): void
    {
        $alumno = $this->alumno(true, ['nombres' => 'PRUEBA '.Str::uuid()]);
        $original = storage_path('app/impresion/'.$alumno->foto_path);
        $this->archivos[] = $original;
        $nombre = Str::upper(Str::slug($alumno->nombres.' '.$alumno->apellidos, '_'));
        $legacy = public_path('uploads/Crey/Estudiantes/SEPTIMO/GENERAL/A/'.$nombre.'.png');
        if (!is_dir(dirname($legacy))) {
            mkdir(dirname($legacy), 0775, true);
        }
        copy($original, $legacy);
        $this->archivos[] = $legacy;
        $alumno->foto_path = null;
        $alumno->save();
        $service = app(FotoService::class);
        $this->assertSame(realpath($legacy), $service->resolve($alumno)['path']);
        unlink($legacy);
        $codigo = 'PRUEBA_'.Str::uuid();
        $archivo = storage_path('app/public/carnetizacion/crey/'.$codigo.'.png');
        if (!is_dir(dirname($archivo))) {
            mkdir(dirname($archivo), 0775, true);
        }
        copy($original, $archivo);
        $this->archivos[] = $archivo;
        $alumno->observaciones = $codigo;
        $this->assertSame(realpath($archivo), $service->resolve($alumno)['path']);
    }
}
