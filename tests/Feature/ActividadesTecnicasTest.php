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
        config(['services.todoist.token'=>null, 'services.todoist.project_id'=>null, 'app.timezone'=>'America/Tegucigalpa','database.default'=>$connection,
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
        Schema::dropIfExists('agendas');
        (require database_path('migrations/2014_10_12_000000_create_users_table.php'))->down();
        (require database_path('migrations/2014_10_12_000000_create_users_table.php'))->up();
        (require database_path('migrations/2025_03_16_105935_create_permission_tables.php'))->up();
        (require database_path('migrations/2025_08_23_084717_create_agendas_table.php'))->up();
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

    public function test_orden_crea_una_actividad_y_editar_conserva_metadatos_y_todoist(): void
    {
        $this->login(); $this->post(route('actividades-tecnicas.ordenes.store'),$this->data(['agenda_tipo'=>'Taller','agenda_responsable'=>'10128774']))->assertRedirect();
        $order=WorkOrder::firstOrFail(); $agenda=$order->agenda;
        $this->assertSame('2026-11-09',substr($agenda->fecha_inicio,0,10));
        $this->assertSame('Taller',$agenda->tipo); $this->assertSame('10128774',(string)$agenda->assigned_user_id);
        $agenda->update(['todoist_task_id'=>'task-existente','contacto'=>'Contacto original','telefono'=>'9999-0000']);
        $this->put(route('actividades-tecnicas.ordenes.update',$order),$this->data(['title'=>'Título actualizado','scheduled_at'=>'2026-11-10T09:30','agenda_tipo'=>'Visita','agenda_responsable'=>'todos']))->assertRedirect();
        $agenda->refresh(); $this->assertSame('Título actualizado',$agenda->titulo);
        $this->assertSame('2026-11-10',substr($agenda->fecha_inicio,0,10)); $this->assertNull($agenda->assigned_user_id);
        $this->assertSame('task-existente',$agenda->todoist_task_id); $this->assertSame('Contacto original',$agenda->contacto);
        $this->assertSame(1,\Modules\Produccion\Entities\Agenda::count());
        $this->get(route('actividades-tecnicas.ordenes.show',$order))->assertOk()->assertSee('Ver en agenda');
    }

    public function test_convertir_actividad_existente_no_duplica_y_conserva_asignacion(): void
    {
        $u=$this->login();
        $agenda=\Modules\Produccion\Entities\Agenda::create(['titulo'=>'Actividad anterior','usuario_id'=>$u->id,'tipo'=>'Visita',
            'fecha_inicio'=>null,'estado'=>1,'servicio'=>'Instalar equipo','contacto'=>'Ana','telefono'=>'9999-1111',
            'assigned_user_id'=>49411725,'todoist_task_id'=>'task-123']);
        $url=route('actividades-tecnicas.ordenes.desde-agenda',$agenda);
        $this->postJson($url)->assertOk()->assertJson(['success'=>true]); $this->postJson($url)->assertOk();
        $this->assertSame(1,WorkOrder::count()); $this->assertSame(1,\Modules\Produccion\Entities\Agenda::count());
        $order=WorkOrder::firstOrFail(); $this->assertSame($agenda->id,(int)$order->agenda_id);
        $this->assertSame('Instalar equipo',$order->description); $this->assertNull($order->scheduled_at);
        $agenda->refresh(); $this->assertSame(49411725,(int)$agenda->assigned_user_id); $this->assertSame('task-123',$agenda->todoist_task_id);
        $this->assertSame('Ana',$agenda->contacto); $this->assertSame(1,$order->statusHistory()->count());
    }

    public function test_agenda_mueve_orden_y_conserva_hora_luego_completa_y_reabre_con_historial(): void
    {
        $this->login(); $this->post(route('actividades-tecnicas.ordenes.store'),$this->data())->assertRedirect();
        $order=WorkOrder::firstOrFail(); $agenda=$order->agenda;
        $this->postJson(route('produccion.admin.agendas.updateDateItem',$agenda),['title'=>'Desde agenda','start'=>'2026-11-12','estado'=>1])->assertOk()->assertJson(['success'=>true]);
        $order->refresh(); $this->assertSame('Desde agenda',$order->title); $this->assertSame('12/11/2026 08:30',$order->scheduledLocal());
        $this->postJson(route('produccion.admin.agendas.updateDateItem',$agenda),['estado'=>2])->assertOk();
        $order->refresh(); $this->assertSame('completed',$order->status); $this->assertNotNull($order->closed_at);
        $this->assertSame(2,$order->statusHistory()->count());
        $this->postJson(route('produccion.admin.agendas.updateDateItem',$agenda),['estado'=>1])->assertOk();
        $order->refresh(); $this->assertSame('new',$order->status); $this->assertNull($order->closed_at);
        $this->assertSame(3,$order->statusHistory()->count());
        $this->assertTrue(TechnicalAuditLog::where('action','sincronizar_agenda')->exists());
    }

    public function test_cancelacion_retira_actividad_y_no_admite_reapertura_desde_agenda(): void
    {
        $u=$this->login(); $order=$this->order($u); $agenda=$order->agenda;
        $this->delete(route('actividades-tecnicas.ordenes.destroy',$order),['confirmar'=>1])->assertRedirect();
        $this->assertSame(2,(int)$agenda->fresh()->estado); $this->assertSame('cancelled',$order->fresh()->status);
        $this->postJson(route('produccion.admin.agendas.updateDateItem',$agenda),['estado'=>1])->assertUnprocessable();
        $this->assertSame(2,(int)$agenda->fresh()->estado); $this->assertSame('cancelled',$order->fresh()->status);
    }

    public function test_permisos_link_y_agenda_independiente(): void
    {
        $admin=$this->login(); $order=$this->order($admin); $agenda=$order->agenda;
        $lector=$this->login(false); $lector->givePermissionTo('actividades-tecnicas.ver');
        $this->postJson(route('produccion.admin.agendas.updateDateItem',$agenda),['title'=>'No autorizado'])->assertForbidden();
        $this->postJson(route('actividades-tecnicas.ordenes.desde-agenda',$agenda))->assertForbidden();
        $this->assertSame('Orden de prueba',$agenda->fresh()->titulo);
        $legacy=new \Modules\Produccion\Entities\Agenda(['titulo'=>'Independiente','usuario_id'=>$lector->id,'estado'=>1]); $legacy->saveQuietly();
        $this->postJson(route('produccion.admin.agendas.updateDateItem',$legacy),['title'=>'Actividad común'])->assertOk();
        $this->assertSame(1,WorkOrder::count());
    }

    public function test_titulo_largo_revierte_ambos_registros_y_las_fechas_vacias_se_sincronizan(): void
    {
        $u=$this->login(); $order=$this->order($u); $agenda=$order->agenda;
        $this->postJson(route('produccion.admin.agendas.updateDateItem',$agenda),['title'=>str_repeat('a',256)])->assertUnprocessable();
        $this->assertSame('Orden de prueba',$agenda->fresh()->titulo); $this->assertSame('Orden de prueba',$order->fresh()->title);
        $agenda->update(['fecha_inicio'=>'2026-11-13']); $this->assertSame('13/11/2026 00:00',$order->fresh()->scheduledLocal());
        $agenda->update(['fecha_inicio'=>null]); $this->assertNull($order->fresh()->scheduled_at);
    }

    public function test_conversion_de_completada_no_reabre_ni_modifica_todoist(): void
    {
        $u=$this->login(); $agenda=\Modules\Produccion\Entities\Agenda::create(['titulo'=>'Ya completada','usuario_id'=>$u->id,'estado'=>2,'fecha_inicio'=>'2026-11-02','todoist_task_id'=>'historica']);
        $this->post(route('actividades-tecnicas.ordenes.desde-agenda',$agenda))->assertRedirect();
        $order=WorkOrder::firstOrFail(); $this->assertSame('completed',$order->status);
        $this->assertSame('completed',$order->statusHistory()->first()->to_status); $this->assertSame('historica',$agenda->fresh()->todoist_task_id);
    }

    public function test_calendario_publica_enlace_y_pantalla_permite_convertir(): void
    {
        $this->login(); $this->post(route('actividades-tecnicas.ordenes.store'),$this->data())->assertRedirect();
        $order=WorkOrder::firstOrFail();
        $this->getJson(route('produccion.admin.agendas.getEvents'))->assertOk()->assertJsonFragment(['orden_url'=>route('actividades-tecnicas.ordenes.show',$order)]);
        $this->get(route('produccion.admin.agendas.index'))->assertOk()->assertSee('Órdenes técnicas')->assertSee('Convertir actividad en orden técnica');
    }

    public function test_comando_integra_ordenes_previas_de_forma_idempotente(): void
    {
        $u=$this->login(); $order=$this->order($u); $old=$order->agenda;
        $order->forceFill(['agenda_id'=>null])->save(); $old->delete();
        $this->artisan('actividades-tecnicas:integrar-agenda')->assertSuccessful();
        $this->artisan('actividades-tecnicas:integrar-agenda')->assertSuccessful();
        $this->assertNotNull($order->fresh()->agenda_id); $this->assertSame(1,\Modules\Produccion\Entities\Agenda::count());
    }

    public function test_nueva_actividad_manual_genera_orden_sin_conversion_ni_llamadas_http(): void
    {
        $u=$this->login(); \Illuminate\Support\Facades\Http::fake();
        $this->postJson(route('produccion.admin.agendas.store'),['title'=>'Nueva desde agenda','tipo'=>'Taller','assigned_user_id'=>'10128774',
            'contacto'=>'Ana','telefono'=>'9999-1111','servicio'=>'Instalar lector'])->assertOk()->assertJson(['success'=>true]);
        $agenda=\Modules\Produccion\Entities\Agenda::firstOrFail(); $order=WorkOrder::firstOrFail();
        $this->assertSame($agenda->id,(int)$order->agenda_id); $this->assertSame('Nueva desde agenda',$order->title);
        $this->assertSame('Instalar lector',$order->description); $this->assertSame($u->id,(int)$order->created_by);
        $this->assertNull($order->scheduled_at); $this->assertSame('new',$order->status);
        $this->assertSame(10128774,(int)$agenda->assigned_user_id); $this->assertSame('Ana',$agenda->contacto);
        $this->assertSame(1,$order->statusHistory()->count()); $this->assertSame(1,TechnicalAuditLog::count());
        $this->postJson(route('actividades-tecnicas.ordenes.desde-agenda',$agenda))->assertOk();
        $this->assertSame(1,WorkOrder::count()); $this->assertSame(1,\Modules\Produccion\Entities\Agenda::count());
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_excel_real_crea_orden_por_fila_y_reimportar_no_duplica(): void
    {
        $this->login(); \Illuminate\Support\Facades\Http::fake(); config(['services.todoist.users.cl'=>10128774]);
        $book=new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $book->getActiveSheet()->fromArray([
            ['secuencia_id_del_ticket','cliente','asunto','asignada_a'],
            ['T-001','Cliente Uno','Instalar equipo','Celvyn Lozano'],
            ['T-002','Cliente Dos','Revisión',''],
            ['T-001','Duplicado','No importar',''],
            ['', 'Sin ticket','No importar',''],
        ]);
        $file=tempnam(sys_get_temp_dir(),'act-excel-');
        try {
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book))->save($file);
            for ($i=0;$i<2;$i++) {
                $upload=new \Illuminate\Http\UploadedFile($file,'actividades.xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',null,true);
                $this->postJson(route('produccion.admin.agenda.import'),['file'=>$upload])->assertOk()->assertJson(['success'=>true]);
            }
        } finally { @unlink($file); $book->disconnectWorksheets(); }
        $this->assertSame(2,WorkOrder::count()); $this->assertSame(2,\Modules\Produccion\Entities\Agenda::count());
        $agenda=\Modules\Produccion\Entities\Agenda::where('secuencia_ticket','T-001')->firstOrFail();
        $this->assertSame('Cliente Uno',$agenda->ordenTecnica->title); $this->assertSame('Instalar equipo',$agenda->ordenTecnica->description);
        $this->assertSame(10128774,(int)$agenda->assigned_user_id); $this->assertNull($agenda->ordenTecnica->scheduled_at);
        $this->assertSame(2,WorkOrderStatusHistory::count()); \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_fallo_de_historial_revierte_nueva_actividad_y_orden(): void
    {
        $u=$this->login(); WorkOrderStatusHistory::creating(function () { throw new \RuntimeException('Historial no disponible'); });
        try {
            \Modules\Produccion\Entities\Agenda::create(['titulo'=>'Revertir','usuario_id'=>$u->id,'estado'=>1]);
            $this->fail('Debió fallar.');
        } catch (\RuntimeException $e) { $this->assertSame('Historial no disponible',$e->getMessage()); }
        finally { WorkOrderStatusHistory::flushEventListeners(); }
        $this->assertSame(0,WorkOrder::count()); $this->assertSame(0,\Modules\Produccion\Entities\Agenda::count());
        $this->assertSame(0,TechnicalAuditLog::count());
    }

    public function test_fila_invalida_revierte_importacion_completa(): void
    {
        $this->login();
        $rows=collect([collect(['secuencia_id_del_ticket'=>'R-001','cliente'=>'Válido']),
            collect(['secuencia_id_del_ticket'=>'R-002','cliente'=>str_repeat('x',256)])]);
        try { (new \Modules\Produccion\Imports\AgendaImport)->collection($rows); $this->fail('Debió validar.'); }
        catch (\Illuminate\Validation\ValidationException $e) { $this->assertArrayHasKey('title',$e->errors()); }
        $this->assertSame(0,WorkOrder::count()); $this->assertSame(0,\Modules\Produccion\Entities\Agenda::count());
    }

    public function test_titulo_de_255_se_conserva_y_orden_creada_directamente_no_genera_ciclo(): void
    {
        $this->login();
        if (DB::getDriverName() === 'mysql') { DB::statement('ALTER TABLE at_work_orders MODIFY title VARCHAR(180) NOT NULL'); }
        (require base_path('Modules/ActividadesTecnicas/Database/Migrations/2026_11_07_000002_expand_work_order_titles.php'))->up();
        $title=str_repeat('á',255);
        $this->postJson(route('produccion.admin.agendas.store'),['title'=>$title])->assertOk();
        $this->assertSame($title,WorkOrder::firstOrFail()->title);
        $this->post(route('actividades-tecnicas.ordenes.store'),$this->data())->assertRedirect();
        $this->assertSame(2,WorkOrder::count()); $this->assertSame(2,\Modules\Produccion\Entities\Agenda::count());
        $this->assertSame(2,WorkOrderStatusHistory::count());
    }

    public function test_reimportar_ticket_anterior_crea_orden_faltante_y_no_cambia_la_actividad(): void
    {
        $u=$this->login(); $agenda=new \Modules\Produccion\Entities\Agenda(['titulo'=>'Actividad anterior','usuario_id'=>$u->id,'estado'=>1,
            'secuencia_ticket'=>'ANT-001','contacto'=>'Ana','servicio'=>'Servicio anterior','todoist_task_id'=>'task-anterior']);
        $agenda->saveQuietly();
        $rows=collect([collect(['secuencia_id_del_ticket'=>'ANT-001','cliente'=>'No reemplazar','asunto'=>'No reemplazar'])]);
        (new \Modules\Produccion\Imports\AgendaImport)->collection($rows);
        (new \Modules\Produccion\Imports\AgendaImport)->collection($rows);
        $this->assertSame(1,WorkOrder::count()); $this->assertSame(1,\Modules\Produccion\Entities\Agenda::count());
        $this->assertSame('Actividad anterior',WorkOrder::firstOrFail()->title);
        $this->assertSame('Servicio anterior',WorkOrder::firstOrFail()->description);
        $this->assertSame('task-anterior',$agenda->fresh()->todoist_task_id); $this->assertSame('Ana',$agenda->fresh()->contacto);
    }

    public function test_health_sanctum_con_permiso_y_rutas_sin_duplicar_prefijo(): void
    {
        $u=$this->login(); Sanctum::actingAs($u,['*']);
        $this->getJson('/api/v1/actividades-tecnicas/health')->assertOk()->assertJson(['module'=>'ActividadesTecnicas','status'=>'ok']);
        $this->assertSame('/admin/actividades-tecnicas/ordenes',parse_url(route('actividades-tecnicas.ordenes.index'),PHP_URL_PATH));
    }
}


