<?php

// app/Services/SnmpService.php
namespace App\Services;

class SnmpService
{
    public function get($ip, $community, $oid)
    {
        return @snmpget($ip, $community, $oid, 1000000, 2);
    }

    public function walk($ip, $community, $oid)
    {
        return @snmpwalk($ip, $community, $oid);
    }
}