@extends('adminlte::page')

@section('title', 'Listado de Equipos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0 font-weight-bold text-dark">Listado de Equipos</h1>
        <div class="btn-group" role="group" aria-label="Acciones de Creación">
            <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" data-toggle="modal" data-target="#marcaModal">
                <i class="fas fa-tag mr-1"></i> Nueva Marca
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-cube mr-1"></i> Nuevo Modelo
            </button>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#crearEquipoModal">
                <i class="fas fa-desktop mr-1"></i> Nuevo Equipo
            </button>
        </div>
    </div>
    <hr class="mt-2 mb-3">
@stop

@section('content')
    {{-- Incluye modales generales de equipos --}}
    @include('netlan::admin.equipos.parciales.modal')

    <div class="row">
        {{-- Tabla de Equipos Activos --}}
        <div class="col-lg-8 col-md-12">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title text-bold text-primary">
                        <i class="fas fa-check-circle mr-2"></i> Equipos Activos
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="equipoActivo" class="table table-bordered table-hover table-striped table-sm w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">Nro</th>
                                    <th>Asignado</th>
                                    <th>IP Address</th>
                                    <th>MAC Address</th>
                                    <th class="text-center">Nomb_pc</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Antivirus</th>
                                    <th class="text-center">Actualizado</th>
                                    <th class="text-center" style="width: 100px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activos as $equipo)
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold text-secondary">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin asignación' }}
                                        </td>
                                        <td class="align-middle"><code>{{ $equipo->ip_address ?? 'N/A' }}</code></td>
                                        <td class="align-middle"><code>{{ $equipo->mac_address ?? 'N/A' }}</code></td>
                                        <td class="align-middle"><code>{{ $equipo->nombre ?? 'N/A' }}</code></td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-secondary">{{ $equipo->tipo }}</span>
                                        </td>
                                        <td class="text-center align-middle">{{ $equipo->antivirus ?? 'N/A' }}</td>
                                        <td class="text-center align-middle">
                                            <button type="button" 
                                                    class="btn btn-xs toggle-estado {{ $equipo->estado == 'Activo' ? 'btn-success' : 'btn-danger' }}"
                                                    data-id="{{ $equipo->id }}">
                                                {{ $equipo->estado == 'Activo' ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-light border">
                                                {{ \Carbon\Carbon::parse($equipo->updated_at)->translatedFormat('M Y') }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ url('netlan/admin/equipos/' . $equipo->id . '/edit') }}"
                                                   class="btn btn-outline-success" 
                                                   title="Editar Equipo">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <form action="{{ url('netlan/admin/equipos', $equipo->id) }}"
                                                      method="POST" 
                                                      class="d-inline form-eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar Equipo">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle mr-1"></i> No hay equipos activos registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de Equipos Inactivos --}}
        <div class="col-lg-4 col-md-12">
            <div class="card card-outline card-danger shadow-sm">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title text-bold text-danger">
                        <i class="fas fa-times-circle mr-2"></i> Equipos Inactivos
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="equipoInactivo" class="table table-bordered table-hover table-striped table-sm w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 40px;">Nro</th>
                                    <th>Asignado</th>
                                    <th>IP / MAC</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Modificado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inActivos as $equipo)
                                    <tr>
                                        <td class="text-center align-middle font-weight-bold text-secondary">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="align-middle">
                                            <small class="font-weight-bold d-block">
                                                {{ $equipo->empleado ? $equipo->empleado->nombres . ' ' . $equipo->empleado->apellidos : 'Sin asignación' }}
                                            </small>
                                        </td>
                                        <td class="align-middle">
                                            <small class="d-block">IP: <code>{{ $equipo->ip_address ?? 'N/A' }}</code></small>
                                            <small class="d-block text-muted">MAC: {{ $equipo->mac_address ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" 
                                                    class="btn btn-xs toggle-estado btn-danger"
                                                    data-id="{{ $equipo->id }}">
                                                Inactivo
                                            </button>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-light border">
                                                {{ \Carbon\Carbon::parse($equipo->updated_at)->translatedFormat('M Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle mr-1"></i> Sin registros inactivos.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Rápido: Modelo --}}
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="crearModeloModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('netlan.admin.modelos.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold" id="crearModeloModalLabel">Agregar Nuevo Modelo</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="marca_id">Marca <span class="text-danger">*</span></label>
                            <select name="marca_id" id="marca_id" class="form-control" required>
                                <option value="" disabled selected>Seleccione una marca</option>
                                @foreach ($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre del Modelo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej. ThinkPad L14" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Rápido: Marca --}}
    <div class="modal fade" id="marcaModal" tabindex="-1" role="dialog" aria-labelledby="crearMarcaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('netlan.admin.marcas.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold" id="crearMarcaModalLabel">Agregar Nueva Marca</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre_marca">Nombre de la Marca <span class="text-danger">*</span></label>
                            <input type="text" id="nombre_marca" class="form-control" name="nombre" placeholder="Ej. Lenovo, HP, Dell" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    @include('netlan::admin.equipos.parciales.css')
@stop

@section('js')
    @include('netlan::admin.equipos.parciales.js')
    
    <script>
        $(document).ready(function() {
            // Confirmación genérica de eliminación con SweetAlert2
            $(document).on('submit', '.form-eliminar', function(e) {
                e.preventDefault();
                const form = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará el registro permanentemente!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmColor: '#d33',
                    cancelColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop



/////////

host=10.85.225.6
type=peer
disallow=all
allow=ulaw&alaw
context=from-trunk
dial=SIP/navega
insecure=very
dtmfmode=rfc2833
qualify=no
nat=yes
languaje=es




//////




issabel*CLI> sip show settings


Global Settings:
----------------
  UDP Bindaddress:        0.0.0.0:5066
  TCP SIP Bindaddress:    Disabled
  TLS SIP Bindaddress:    Disabled
  RTP Bindaddress:        Disabled
  Videosupport:           No
  Textsupport:            No
  Ignore SDP sess. ver.:  No
  AutoCreate Peer:        Off
  Match Auth Username:    No
  Allow unknown access:   No
  Allow subscriptions:    Yes
  Allow overlap dialing:  Yes
  Allow promisc. redir:   No
  Enable call counters:   No
  SIP domain support:     No
  Path support :          No
  Realm. auth:            No
  Our auth realm          asterisk
  Use domains as realms:  No
  Call to non-local dom.: Yes
  URI user is phone no:   No
  Always auth rejects:    Yes
  Direct RTP setup:       No
  User Agent:             IPBX-2.12.0.0(18.19.0)
  SDP Session Name:       Asterisk PBX 18.19.0
  SDP Owner Name:         root
  Reg. context:           (not set)
  Regexten on Qualify:    No
  Trust RPID:             No
  Send RPID:              No
  Legacy userfield parse: No
  Send Diversion:         Yes
  Caller ID:              Unknown
  From: Domain:
  Record SIP history:     Off
  Auth. Failure Events:   Off
  T.38 support:           No
  T.38 EC mode:           Unknown
  T.38 MaxDtgrm:          4294967295
  SIP realtime:           Disabled
  Qualify Freq :          60000 ms
  Q.850 Reason header:    No
  Store SIP_CAUSE:        No

Network QoS Settings:
---------------------------
  IP ToS SIP:             CS3
  IP ToS RTP audio:       EF
  IP ToS RTP video:       AF41
  IP ToS RTP text:        CS0
  802.1p CoS SIP:         4
  802.1p CoS RTP audio:   5
  802.1p CoS RTP video:   6
  802.1p CoS RTP text:    5
  Jitterbuffer enabled:   No

Network Settings:
---------------------------
  SIP address remapping:  Disabled, no localnet list
  Externhost:             <none>
  Externaddr:             (null)
  Externrefresh:          10

Global Signalling Settings:
---------------------------
  Codecs:                 (ulaw|alaw|gsm|opus|g729)
  Relax DTMF:             No
  RFC2833 Compensation:   No
  Symmetric RTP:          No
  Compact SIP headers:    No
  RTP Keepalive:          0 (Disabled)
  RTP Timeout:            0 (Disabled)
  RTP Hold Timeout:       0 (Disabled)
  MWI NOTIFY mime type:   application/simple-message-summary
  DNS SRV lookup:         Yes
  Pedantic SIP support:   Yes
  Reg. min duration       60 secs
  Reg. max duration:      3600 secs
  Reg. default duration:  120 secs
  Sub. min duration       60 secs
  Sub. max duration:      3600 secs
  Outbound reg. timeout:  20 secs
  Outbound reg. attempts: 0
  Outbound reg. retry 403:No
  Notify ringing state:   Yes
    Include CID:          No
  Notify hold state:      Yes
  SIP Transfer mode:      open
  Max Call Bitrate:       384 kbps
  Auto-Framing:           No
  Outb. proxy:            <not set>
  Session Timers:         Accept
  Session Refresher:      uas
  Session Expires:        1800 secs
  Session Min-SE:         90 secs
  Timer T1:               500
  Timer T1 minimum:       100
  Timer B:                32000
  No premature media:     Yes
  Max forwards:           70

Default Settings:
-----------------
  Allowed transports:     UDP
  Outbound transport:     UDP
  Context:                from-sip-external
  Record on feature:      automon
  Record off feature:     automon
  Force rport:            Auto (No)
  DTMF:                   rfc2833
  Qualify:                0
  Keepalive:              0
  Use ClientCode:         No
  Progress inband:        No
  Language:               es
  Tone zone:              <Not set>
  MOH Interpret:          default
  MOH Suggest:
  Voice Mail Extension:   *97
  RTCP Multiplexing:      No



  
15:27:28.358426 IP 10.85.225.6.sip > 10.101.111.34.stanag-5066: SIP: SIP/2.0 500 Server Internal Error
E..e..@.<.R.
U..
eo".....QmtSIP/2.0 500 Server Internal Error
Via: SIP/2.0/UDP 10.101.111.34:5066;branch=z9hG4bK622ec378
To: <sip:50422160200@10.85.225.6:5060>;tag=npi4o8tmj4ipis512zgwx69i67fq2mss
From: <sip:22694416@10.101.111.34:5066>;tag=as548f4ab7
Call-ID: 0b0683882371af7600e090574452908e@10.101.111.34:5066
CSeq: 102 INVITE
Content-Length: 0
