<?php

namespace Modules\Mikrotik\Services;

use RouterOS\{APIConnector, Query};

class ProtocoloApi
{
    private APIConnector $connector;

    public function __construct(private FlujoApi $flujo)
    {
        $this->connector = new APIConnector($flujo);
    }

    public function intercambiar(Query $query): array
    {
        $this->flujo->iniciar();
        foreach ($query->getQuery() as $word) { $this->connector->writeWord($word); }
        $this->connector->writeWord('');
        $raw = [];
        $terminada = false;
        do {
            $word = $this->connector->readWord();
            if ($word === '' && $terminada) { break; }
            if ($word !== '') { $raw[] = $word; }
            if ($word === '!done' || $word === '!fatal') { $terminada = true; }
        } while (true);
        return RespuestaApi::filas($raw);
    }
}
