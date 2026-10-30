<?php
namespace Modules\Produccion\Imports;

use Modules\Produccion\Entities\Agenda;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AgendaImport implements ToCollection, WithHeadingRow
{
    private array $envUserMap;

    public function __construct()
    {
        // Mapeo de nombres del Excel hacia los IDs configurados en tu .env
        $this->envUserMap = [
            'kevyn'   => config('services.todoist.kg', env('TODOIST_KG')),
            'kevin'   => config('services.todoist.kg', env('TODOIST_KG')),
            'celvyn'  => config('services.todoist.cl', env('TODOIST_CL')),
            'luis'    => config('services.todoist.ld', env('TODOIST_LD')),
            'harling' => config('services.todoist.hm', env('TODOIST_HM')),
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // "secuencia_id_del_ticket" es la columna "Secuencia ID del ticket" en minúsculas/snake_case
            $secuenciaTicket = isset($row['secuencia_id_del_ticket']) 
                ? trim((string) $row['secuencia_id_del_ticket']) 
                : null;

            // Validar si la secuencia del ticket ya existe en la BD o viene vacía para omitir duplicados
            if (!$secuenciaTicket || Agenda::where('secuencia_ticket', $secuenciaTicket)->exists()) {
                continue;
            }

            // Resolver asignación de usuario id
            $assignedUserId = $this->resolveUserIdFromEnv($row['asignada_a'] ?? null);
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

    /**
     * Mapea la cadena de "Asignada a" con el ID del .env correspondiente
     */
    private function resolveUserIdFromEnv(?string $nombre): ?int
    {
        if (empty($nombre)) return null;

        $nombreLower = mb_strtolower(trim($nombre));

        foreach ($this->envUserMap as $key => $id) {
            if (str_contains($nombreLower, $key) && !empty($id)) {
                return (int) $id;
            }
        }

        return null;
    }

    /**
     * Helper para color de evento (replica tu controlador)
     */
    private function getColorByTipo(string $tipo): string
    {
        return match ($tipo) {
            'Llamada' => '#3788d8',
            'Reunión' => '#28a745',
            default   => '#6c757d',
        };
    }
}