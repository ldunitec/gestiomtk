<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\SnmpService;

class DeviceController extends Controller
{
    public function index(SnmpService $snmp)
    {
        $devices = Device::all();

        foreach ($devices as $device) {
            $device->uptime = $snmp->get(
                $device->ip,
                $device->community,
                '1.3.6.1.2.1.1.3.0'
            );
        }

        return view('admin.snmp.index', compact('devices'));
    }

    public function show(Device $device, SnmpService $snmp)
    {
        return response()->json([
            'hostname' => $snmp->get($device->ip, $device->community, '1.3.6.1.2.1.1.5.0'),
            'description' => $snmp->get($device->ip, $device->community, '1.3.6.1.2.1.1.1.0'),
            'uptime' => $snmp->get($device->ip, $device->community, '1.3.6.1.2.1.1.3.0'),
            'contact' => $snmp->get($device->ip, $device->community, '1.3.6.1.2.1.1.4.0'),
            'location' => $snmp->get($device->ip, $device->community, '1.3.6.1.2.1.1.6.0'),
        ]);
    }
}