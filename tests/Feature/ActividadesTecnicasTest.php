<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\{Carbon, CarbonImmutable};
use Illuminate\Support\Facades\{DB, Schema};
use Laravel\Sanctum\Sanctum;
use Modules\ActividadesTecnicas\Database\Seeders\ActividadesTecnicasDatabaseSeeder;
use Modules\ActividadesTecnicas\Models\{WorkOrder, WorkOrderStatusHistory, TechnicalAuditLog, ServiceClient, ServiceLocation, ServiceType, TechProfile};
use Modules\ActividadesTecnicas\Services\WorkOrderService;
use Spatie\Permission\Models\{Permission, Role};
use Tests\TestCase;

class ActividadesTecnicasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $connection = getenv('ACT_TEST_DB') === 'mysql' ? 'mysql' : 'sqlite';
        config(['app.timezone'=>'America/Tegucigalpa','database.default'=>$connection,
            'database.connections.sqlite.database'=>':memory:', 'database.connections.sqlite.foreign_key_constraints'=>true,
            'database.connections.mysql.host'=>'127.0.0.1','database.connections.mysql.port'=>3306,
            'database.connections.mysql.database'=>'actividades_test','database.connections.mysql.username'=>'root',
            'database.connections.mysql.password'=>'testing']);
        DB::purge($connection);
        $migrations = glob(base_path('Modules/ActividadesTecnicas/Database/Migrations/*.php'));
        foreach (array_reverse($migrations) as $file) { (require $file)->down(); }
        if (Schema::hasTable('role_has_permissions')) {
            (require database_path('migrations/2025_03_16_105935_create_permission_tables.php'))->down();
        }
        (require database_path('migrations/2014_10_12_000000_create_users_table.php'))->down();
        (require database_path('migrations/2014_10_12_000000_create_users_table.php'))->up();
        (require database_path('migrations/2025_03_16_105935_create_permission_tables.php'))->up();
        foreach ($migrations as $file) { (require $file)->up(); }
        Carbon::setTestNow(Carbon::parse('2026-11-07 18:00:00', 'UTC'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-07 18:00:00', 'UTC'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(); CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function login(bool $admin = true): User
    {
        $u = User::create(['name'=>'Luis Díaz','email'=>uniqid('operador').'@example.test','password'=>'testing-password']);
        if ($admin) {
            Role::findOrCreate('ADMINISTRADOR','web');
            $this->seed(ActividadesTecnicasDatabaseSeeder::class);
            $u->assignRole('ADMINISTRADOR');
        }
        $this->actingAs($u);
        return $u;
    }

    private function data(array $extra = []): array
    {
        return array_merge(['title'=>'Instalar control de acceso','description'=>'Revisar cableado y puesta en marcha',
            'priority'=>'high','scheduled_at'=>'2026-11-09T08:30'], $extra);
    }

    private function order(User $u): WorkOrder
    {
        return app(WorkOrderService::class)->create(['title'=>'Orden de prueba','priority'=>'normal'], $u->id);
    }

    public function test_rutas_exigen_sesion_y_permiso_y_api_no_es_publica(): void
    {
        $this->getJson(route('actividades-tecnicas.dashboard'))->assertUnauthorized();
        $this->postJson(route('actividades-tecnicas.ordenes.store'),$this->data())->assertUnauthorized();
        $this->getJson('/api/v1/actividades-tecnicas/health')->assertUnauthorized();
        $this->login(false);
        $this->getJson(route('actividades-tecnicas.dashboard'))->assertForbidden();
        $this->getJson(route('actividades-tecnicas.ordenes.create'))->assertForbidden();
        $this->postJson(route('actividades-tecnicas.ordenes.store'),$this->data())->assertForbidden();
    }

    public function test_seeder_aditivo_idempotente_y_menu_activado(): void
    {
        $rol=Role::create(['name'=>'ADMINISTRADOR','guard_name'=>'web']);
        $rol->givePermissionTo(Permission::create(['name'=>'existente','guard_name'=>'web']));
        $this->seed(ActividadesTecnicasDatabaseSeeder::class); $this->seed(ActividadesTecnicasDatabaseSeeder::class);
        $this->assertSame(4,Permission::count()); $this->assertTrue($rol->fresh()->hasPermissionTo('existente'));
        $this->assertTrue($rol->fresh()->hasPermissionTo('actividades-tecnicas.ver'));
        $this->login(); $this->get(route('actividades-tecnicas.dashboard'))->assertOk()->assertSee('Actividades técnicas');
        $this->assertTrue(json_decode(file_get_contents(base_path('modules_statuses.json')),true)['ActividadesTecnicas']);
    }

    public function test_crea_orden_historial_auditoria_fecha_y_numero(): void
    {
        $u=$this->login();
        $this->post(route('actividades-tecnicas.ordenes.store'),$this->data(['created_by'=>999,'status'=>'approved','order_number'=>'INYECTADO']))->assertRedirect();
        $order=WorkOrder::firstOrFail();
        $this->assertSame('new',$order->status); $this->assertSame($u->id,(int)$order->created_by);
        $this->assertMatchesRegularExpression('/^OT-20261107-\d{6,}$/',$order->order_number);
        $this->assertSame('2026-11-09 14:30:00',$order->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertSame('09/11/2026 08:30',$order->scheduledLocal());
        $this->assertSame(1,$order->statusHistory()->count());
        $this->assertNotNull($order->statusHistory()->first()->changed_at);
        $this->assertSame('crear_orden',TechnicalAuditLog::first()->action);
        $this->get(route('actividades-tecnicas.ordenes.show',$order))->assertOk()->assertSee('09/11/2026 08:30')->assertSee('Orden creada');
    }

    public function test_pantallas_lista_edicion_validacion_y_escape(): void
    {
        $u=$this->login(); $order=$this->order($u);
        foreach (['dashboard','ordenes.index','ordenes.create'] as $name) { $this->get(route('actividades-tecnicas.'.$name))->assertOk(); }
        $this->get(route('actividades-tecnicas.ordenes.edit',$order))->assertOk();
        $this->put(route('actividades-tecnicas.ordenes.update',$order),$this->data(['title'=>'<script>Alerta</script>','priority'=>'urgent']))->assertRedirect();
        $order->refresh(); $this->assertSame('urgent',$order->priority);
        $this->assertSame('editar_orden',TechnicalAuditLog::latest('id')->first()->action);
        $this->get(route('actividades-tecnicas.ordenes.show',$order))->assertOk()->assertSee('<script>Alerta</script>')->assertDontSee('<script>Alerta</script>',false);
        $this->get(route('actividades-tecnicas.ordenes.edit',$order))->assertOk()->assertSee('2026-11-09T08:30');
        $this->postJson(route('actividades-tecnicas.ordenes.store'),$this->data(['title'=>'','priority'=>'admin','scheduled_at'=>'ayer']))->assertUnprocessable();
        $this->assertSame(1,WorkOrder::count());
    }

    public function test_solo_lectura_no_crea_edita_ni_cancela(): void
    {
        $admin=$this->login(); $order=$this->order($admin);
        $lector=$this->login(false); $lector->givePermissionTo('actividades-tecnicas.ver');
        $this->get(route('actividades-tecnicas.ordenes.index'))->assertOk()->assertDontSee('Crear orden');
        $this->get(route('actividades-tecnicas.ordenes.show',$order))->assertOk()->assertDontSee('Cancelar orden')->assertDontSee('>Editar</a>',false);
        $this->getJson(route('actividades-tecnicas.ordenes.create'))->assertForbidden();
        $this->putJson(route('actividades-tecnicas.ordenes.update',$order),$this->data())->assertForbidden();
        $this->deleteJson(route('actividades-tecnicas.ordenes.destroy',$order),['confirmar'=>1])->assertForbidden();
    }

    public function test_cancela_con_historial_sin_borrado_y_repeticion_no_duplica(): void
    {
        $u=$this->login(); $order=$this->order($u); $url=route('actividades-tecnicas.ordenes.destroy',$order);
        $this->deleteJson($url,[])->assertUnprocessable();
        $this->delete($url,['confirmar'=>1])->assertRedirect(); $this->delete($url,['confirmar'=>1])->assertRedirect();
        $order->refresh(); $this->assertSame('cancelled',$order->status); $this->assertNotNull($order->closed_at);
        $this->assertSame(2,$order->statusHistory()->count()); $this->assertSame(2,TechnicalAuditLog::count());
        $this->assertSame(1,WorkOrder::count());
        $this->putJson(route('actividades-tecnicas.ordenes.update',$order),$this->data())->assertUnprocessable();
        $this->get(route('actividades-tecnicas.ordenes.show',$order))->assertOk()->assertSee('Cancelada');
    }

    public function test_no_cancela_orden_en_ejecucion(): void
    {
        $u=$this->login(); $order=$this->order($u); $order->update(['status'=>'in_progress']);
        $this->deleteJson(route('actividades-tecnicas.ordenes.destroy',$order),['confirmar'=>1])->assertUnprocessable();
        $this->assertSame('in_progress',$order->fresh()->status); $this->assertSame(1,$order->statusHistory()->count());
    }

    public function test_creacion_revierte_si_falla_historial(): void
    {
        $u=$this->login();
        WorkOrderStatusHistory::creating(function () { throw new \RuntimeException('Fallo simulado de historial'); });
        try { app(WorkOrderService::class)->create(['title'=>'No persistir','priority'=>'normal'],$u->id); $this->fail('Debió fallar.'); }
        catch (\RuntimeException $e) { $this->assertSame('Fallo simulado de historial',$e->getMessage()); }
        finally { WorkOrderStatusHistory::flushEventListeners(); }
        $this->assertSame(0,WorkOrder::count()); $this->assertSame(0,TechnicalAuditLog::count());
    }

    public function test_numeracion_no_reutiliza_numeros_tras_cancelacion_y_borrado_de_laboratorio(): void
    {
        $u=$this->login(); $a=$this->order($u); $b=$this->order($u); app(WorkOrderService::class)->cancel($b,$u->id);
        $numeroB=$b->order_number; $b->delete(); $c=$this->order($u);
        $this->assertNotSame($numeroB,$c->order_number); $this->assertNotSame($a->order_number,$c->order_number);
    }

    public function test_modelos_relaciones_y_casts_del_adjunto_funcionan_en_laravel10(): void
    {
        $u=$this->login(); $order=$this->order($u);
        $client=ServiceClient::create(['name'=>'Cliente','is_active'=>true]);
        $location=ServiceLocation::create(['client_id'=>$client->id,'name'=>'Tegucigalpa','address'=>'Centro','latitude'=>'14.0723000','longitude'=>'-87.1921000']);
        $type=ServiceType::create(['name'=>'Instalación','default_duration_minutes'=>60]);
        TechProfile::create(['user_id'=>$u->id,'is_active'=>true]);
        $order->update(['client_id'=>$client->id,'location_id'=>$location->id,'service_type_id'=>$type->id]);
        $order->assignments()->create(['technician_id'=>$u->id,'assigned_by'=>$u->id,'assigned_at'=>now()]);
        $order->timeLogs()->create(['technician_id'=>$u->id,'event'=>'start','started_at'=>now()]);
        $order->gpsLogs()->create(['technician_id'=>$u->id,'event'=>'arrival','latitude'=>'14.0723000','longitude'=>'-87.1921000','recorded_at'=>now()]);
        $order->evidences()->create(['uploaded_by'=>$u->id,'kind'=>'before','path'=>'privado/prueba.jpg','captured_at'=>now()]);
        $order->materials()->create(['technician_id'=>$u->id,'material_name'=>'Cable','quantity'=>'2.500','unit_cost'=>'12.30']);
        $order->approvals()->create(['approved_by_name'=>'Cliente','approved_at'=>now()]);
        $this->assertSame($client->id,$order->client->id); $this->assertTrue($client->is_active);
        $this->assertSame('14.0723000',$location->latitude); $this->assertSame(60,$type->default_duration_minutes);
        foreach (['assignments','timeLogs','gpsLogs','evidences','materials','approvals'] as $rel) { $this->assertSame(1,$order->$rel()->count()); }
        $this->assertInstanceOf(\DateTimeInterface::class,$order->timeLogs()->first()->started_at);
        $this->assertSame('2.500',$order->materials()->first()->quantity);
        $this->get(route('actividades-tecnicas.ordenes.show',$order))->assertOk()->assertSee('Cliente')->assertSee('Centro');
        $tables=array_filter(Schema::getTableListing(),fn ($t)=>str_starts_with($t,'at_'));
        $this->assertCount(13,$tables); $this->assertFalse(Schema::hasTable('work_orders'));
    }

    public function test_health_sanctum_con_permiso_y_rutas_sin_duplicar_prefijo(): void
    {
        $u=$this->login(); Sanctum::actingAs($u,['*']);
        $this->getJson('/api/v1/actividades-tecnicas/health')->assertOk()->assertJson(['module'=>'ActividadesTecnicas','status'=>'ok']);
        $this->assertSame('/admin/actividades-tecnicas/ordenes',parse_url(route('actividades-tecnicas.ordenes.index'),PHP_URL_PATH));
    }
}
