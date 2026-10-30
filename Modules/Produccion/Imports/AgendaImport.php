<?php
namespace Modules\Produccion\Imports;

use Modules\Produccion\Entities\Agenda;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgendaImport implements ToCollection, WithHeadingRow
{
    private array $envUserMap;

    public function __construct()
{
    $this->envUserMap = [
        'kevyn'         => config('services.todoist.users.kg'),
        'Kevin Godoy'   => config('services.todoist.users.kg'),
        'Celvyn Lozano' => config('services.todoist.users.cl'),
        'Luis Diaz'     => config('services.todoist.users.ld'),
        'harling'       => config('services.todoist.users.hm'),
    ];
}

    public function collection(Collection $rows)
    {

    
        foreach ($rows as $row) {


     $nombreAsignado = $row['asignada_a'] ?? null;


            // Leer y validar la secuencia del ticket
            $secuenciaTicket = trim(
                (string) ($row['secuencia_id_del_ticket'] ?? '')
            );

            if (
                $secuenciaTicket === '' ||
                Agenda::where('secuencia_ticket', $secuenciaTicket)->exists()
            ) {
                continue;
            }

            // Leer la persona asignada desde el Excel
            $nombreAsignado = $row['asignada_a'] ?? null;

            // Resolver el ID configurado
            $assignedUserId = $this->resolveUserIdFromEnv(
                $nombreAsignado
            );

            // Registrar datos para diagnosticar asignaciones
            if ($nombreAsignado !== null && $assignedUserId === null) {
                Log::warning('AgendaImport: usuario no identificado', [
                    'asignada_a' => $nombreAsignado,
                    'encabezados' => array_keys($row->toArray()),
                ]);
            }

            $tipo = 'Llamada';

           

            Agenda::create([
                'titulo'           => $row['cliente'] ?? 'Sin título',
                'fecha_inicio'     => null,
                'fecha_fin'        => null,
                'color'            => $this->getColorByTipo($tipo),
                'usuario_id'       => Auth::id(),
                'assigned_user_id' => $assignedUserId,
                'estado'           => 1,
                'tipo'             => $tipo,
                'contacto'         => null,
                'telefono'         => null,
                'servicio'         => $row['asunto'] ?? null,
                'proyecto'         => null,
                'secuencia_ticket' => $secuenciaTicket,
            ]);
        }
    }

    private function normalizeName(?string $nombre): string
    {
        return mb_strtolower(trim((string) $nombre), 'UTF-8');
    }

    private function resolveUserIdFromEnv($nombre): ?int
    {
        if ($nombre === null || trim((string) $nombre) === '') {
            return null;
        }

        $nombreLower = $this->normalizeName((string) $nombre);

        foreach ($this->envUserMap as $key => $id) {
            $keyLower = $this->normalizeName($key);

            if (
                str_contains($nombreLower, $keyLower) &&
                is_numeric($id) &&
                (int) $id > 0
            ) {
                return (int) $id;
            }
        }

        return null;
    }

    private function getColorByTipo(string $tipo): string
    {
        return match ($tipo) {
            'Llamada' => '#3788d8',
            'Reunión' => '#28a745',
            default   => '#6c757d',
        };
    }
}
