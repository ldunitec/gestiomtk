<?php

return [
    'timezone' => 'America/Tegucigalpa',
    // CA del certificado API-SSL, si no está en el almacén de confianza de PHP.
    'ca_file' => env('MIKROTIK_VISITANTES_CA_FILE'),
];
