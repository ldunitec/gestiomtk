<?php

namespace Tests\Unit;

use Modules\Mikrotik\Services\{ErrorRouter, FlujoApi, ProtocoloApi, RespuestaApi, RouterApi};
use PHPUnit\Framework\TestCase;
use RouterOS\{APILengthCoDec, Query};
use Tests\Support\FlujoFragmentado;

class MikrotikTransporteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        stream_wrapper_register('gmtkfragmentado', FlujoFragmentado::class);
        FlujoFragmentado::$entrada = '';
        FlujoFragmentado::$salida = '';
    }

    protected function tearDown(): void
    {
        stream_wrapper_unregister('gmtkfragmentado');
        \Mockery::close();
        parent::tearDown();
    }

    private function wire(array $frases): string
    {
        $wire = '';
        foreach ($frases as $frase) {
            foreach ($frase as $word) { $wire .= APILengthCoDec::encodeLength(strlen($word)).$word; }
            $wire .= "\0";
        }
        return $wire;
    }

    private function flujo(): FlujoApi
    {
        return new FlujoApi(fopen('gmtkfragmentado://router', 'r+'));
    }

    public function test_lee_palabra_completa_aunque_llegue_en_fragmentos(): void
    {
        FlujoFragmentado::$entrada = 'abcdefghijklmnop';
        $flujo = $this->flujo();
        $this->assertSame('abcdefghijklmnop', $flujo->read(16));
        $flujo->close();
    }

    public function test_envia_todos_los_bytes_aunque_fwrite_escriba_parcial(): void
    {
        $flujo = $this->flujo();
        $datos = str_repeat("\0\x80linea\n", 500);
        $this->assertSame(strlen($datos), $flujo->write($datos));
        $this->assertSame($datos, FlujoFragmentado::$salida);
        $flujo->close();
    }

    public function test_respuesta_larga_del_script_conserva_limites_y_siguiente_comando(): void
    {
        $source = str_repeat(":local texto \"a=b\";\n", 400);
        FlujoFragmentado::$entrada = $this->wire([
            ['!re', '=.id=*A', '=source='.$source], ['!done'],
            ['!re', '=name=hotspot'], ['!done'],
        ]);
        $flujo = $this->flujo();
        $p = new ProtocoloApi($flujo);
        $q = (new Query('/system/script/print'))->equal('.proplist', '.id,source');
        $this->assertSame([['.id'=>'*A', 'source'=>$source]], $p->intercambiar($q));
        $this->assertSame([['name'=>'hotspot']], $p->intercambiar(new Query('/system/identity/print')));
        $esperada = $this->wire([$q->getQuery(), ['/system/identity/print']]);
        $this->assertSame($esperada, FlujoFragmentado::$salida);
        $flujo->close();
    }

    public function test_envia_source_multilinea_y_acepta_done_con_ret(): void
    {
        $source = str_repeat(":local x \"==\";\n", 400);
        FlujoFragmentado::$entrada = $this->wire([['!done', '=ret=*17']]);
        $flujo = $this->flujo();
        $q = (new Query('/system/script/add'))->equal('name', 'gmtk-prueba')->equal('source', $source);
        $this->assertSame([], (new ProtocoloApi($flujo))->intercambiar($q));
        $this->assertSame($this->wire([$q->getQuery()]), FlujoFragmentado::$salida);
        $flujo->close();
    }

    public function test_interrupcion_a_mitad_de_palabra_no_se_reporta_como_exito(): void
    {
        FlujoFragmentado::$entrada = APILengthCoDec::encodeLength(20).'respuesta_cortada';
        $flujo = $this->flujo();
        try {
            $this->expectException(ErrorRouter::class);
            (new ProtocoloApi($flujo))->intercambiar(new Query('/system/script/print'));
        } finally { $flujo->close(); }
    }

    public function test_trap_de_permisos_se_lee_hasta_done_y_no_revela_mensaje_crudo(): void
    {
        FlujoFragmentado::$entrada = $this->wire([['!trap', '=message=not enough permissions CLAVE-QUE-NO-DEBE-MOSTRARSE'], ['!done']]);
        $flujo = $this->flujo();
        try {
            (new ProtocoloApi($flujo))->intercambiar(new Query('/system/script/add'));
            $this->fail('Debió rechazar el comando.');
        } catch (ErrorRouter $e) {
            $this->assertStringContainsString('permisos suficientes', $e->getMessage());
            $this->assertStringNotContainsString('CLAVE-QUE-NO-DEBE-MOSTRARSE', $e->getMessage());
        } finally { $flujo->close(); }
    }

    public function test_error_de_sintaxis_muestra_linea_y_columna_sin_source(): void
    {
        try {
            RespuestaApi::filas(['!trap', '=message=syntax error (line 12 column 5) password=SECRETO', '!done']);
            $this->fail('Debió rechazar la sintaxis.');
        } catch (ErrorRouter $e) {
            $this->assertStringContainsString('Línea 12, columna 5', $e->getMessage());
            $this->assertStringNotContainsString('SECRETO', $e->getMessage());
        }
    }

    public function test_router_api_agrega_solo_ruta_al_error_sin_atributos_sensibles(): void
    {
        $p = \Mockery::mock(ProtocoloApi::class);
        $p->shouldReceive('intercambiar')->once()->andThrow(new ErrorRouter('Tiempo de espera agotado.'));
        $r = new \ReflectionClass(RouterApi::class);
        $api = $r->newInstanceWithoutConstructor();
        $r->getProperty('protocolo')->setValue($api, $p);
        try {
            $api->ejecutar('/system/script/add', ['source'=>'CODIGO-SECRETO', 'password'=>'CLAVE-SECRETA']);
            $this->fail('Debió informar la operación fallida.');
        } catch (ErrorRouter $e) {
            $this->assertStringContainsString('/system/script/add', $e->getMessage());
            $this->assertStringNotContainsString('CODIGO-SECRETO', $e->getMessage());
            $this->assertStringNotContainsString('CLAVE-SECRETA', $e->getMessage());
        }
    }
}
