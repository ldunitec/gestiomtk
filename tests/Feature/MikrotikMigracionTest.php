<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\{DB, Schema};
use Tests\TestCase;

class MikrotikMigracionTest extends TestCase
{
    public function test_actualiza_mariadb_con_fichas_antiguas_sin_cambiar_sus_fechas(): void
    {
        if (!getenv('MIKROTIK_TEST_MYSQL')) { $this->markTestSkipped('Requiere MariaDB aislada de CI.'); }
        config(['database.default'=>'mysql', 'database.connections.mysql.host'=>'127.0.0.1',
            'database.connections.mysql.database'=>'mikrotik_test', 'database.connections.mysql.username'=>'root',
            'database.connections.mysql.password'=>'testing', 'database.connections.mysql.port'=>3306]);
        DB::purge('mysql');
        $base = require base_path('Modules/Mikrotik/Database/Migrations/2026_11_03_000001_create_mikrotik_visitantes_tables.php');
        $nueva = require base_path('Modules/Mikrotik/Database/Migrations/2026_11_05_000001_add_inicio_vigencia_to_mikrotik.php');
        $base->down();
        try {
            $base->up();
            // Reproduce exactamente la restricción de la versión instalada anterior.
            DB::statement('ALTER TABLE mt_lotes MODIFY vence_at DATETIME NOT NULL');
            DB::statement('ALTER TABLE mt_fichas MODIFY vence_at DATETIME NOT NULL');
            $router = DB::table('mt_routers')->insertGetId(['uuid'=>'00000000-0000-4000-8000-000000000001',
                'nombre'=>'Migración', 'host'=>'127.0.0.1', 'usuario_api'=>'test', 'clave_api'=>'cifrada',
                'servidor_hotspot'=>'visitantes','portal_url'=>'http://wifi.test/login','ssid'=>'test']);
            $lote = DB::table('mt_lotes')->insertGetId(['solicitud_uuid'=>'00000000-0000-4000-8000-000000000002',
                'router_id'=>$router,'dias'=>1,'cantidad'=>1,'creado_por'=>1,'vence_at'=>'2026-11-06 18:00:00']);
            DB::table('mt_fichas')->insert(['router_id'=>$router,'lote_id'=>$lote,'usuario'=>'V-ANTERIOR',
                'clave'=>'cifrada','estado'=>'activa','vence_at'=>'2026-11-06 18:00:00']);
            $nueva->up();
            (require base_path('Modules/Mikrotik/Database/Migrations/2026_11_05_000002_add_impresion_to_mt_fichas.php'))->up();
            $anterior=DB::table('mt_fichas')->first();
            $this->assertSame('emision',$anterior->inicio_vigencia);
            $this->assertSame('2026-11-06 18:00:00',$anterior->vence_at);
            $this->assertNull($anterior->activada_at);
            $this->assertNull($anterior->impresa_at);
            $this->assertNull($anterior->deleted_at);
            DB::table('mt_fichas')->where('id',$anterior->id)->update(['nombre'=>'Luis Díaz', 'impresa_at'=>'2026-11-05 20:00:00', 'impresa_por'=>1]);
            $this->assertSame('Luis Díaz', DB::table('mt_fichas')->where('id',$anterior->id)->value('nombre'));
            foreach (['primer_ingreso','permanente'] as $modo) {
                $nuevo=DB::table('mt_lotes')->insertGetId(['solicitud_uuid'=>(string) \Illuminate\Support\Str::uuid(),
                    'router_id'=>$router,'dias'=>$modo==='permanente'?0:1,'cantidad'=>1,'creado_por'=>1,
                    'inicio_vigencia'=>$modo,'vence_at'=>null]);
                DB::table('mt_fichas')->insert(['router_id'=>$router,'lote_id'=>$nuevo,'usuario'=>$modo,
                    'clave'=>'cifrada','inicio_vigencia'=>$modo,'vence_at'=>null]);
            }
            $this->assertSame(2,DB::table('mt_fichas')->whereNull('vence_at')->count());
            $this->assertTrue(Schema::hasColumn('mt_fichas','duracion_dias'));
        } finally { $base->down(); }
    }
}

