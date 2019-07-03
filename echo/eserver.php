<?php
require_once '../lib/CommonUtils.php';

$ipaddress = '127.0.0.1';
$ipport = '8099';
$ipparam = isset($argv['1']) ? $argv['1'] : '';
if (!empty($ipparam)) {
    $ipparam = explode(':', $ipparam);
    $ipaddress = isset($ipparam['0']) ? $ipparam['0'] : $ipaddress;
    $ipport = isset($ipparam['1']) ? $ipparam['1'] : $ipport;
}

// step1: create a socket
$skt = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
Yps_CommonUtils::failCheck($skt, false, "create a socket");

// step2: bind to ip:port
$isbind = socket_bind($skt, $ipaddress, $ipport);
Yps_CommonUtils::failCheck($isbind, false, "bind to [{$ipaddress}:{$ipport}]");

// step3: listen for connection
$islisten = socket_listen($skt, 1024);
Yps_CommonUtils::failCheck($islisten, false, "listen for connection");

// step4: access connection and handle it
while(true) {
    $cskt = socket_accept($skt);
    Yps_CommonUtils::failCheck($cskt, false, "access a connection");

    socket_getpeername($cskt, $csktaddr, $csktport);
    printf("accept connection from [{$csktaddr}:{$csktport}]\n");

    handle_connection($cskt, $csktaddr, $csktport);
}

// step5: close socket
socket_close($skt);
Yps_CommonUtils::failCheck($islisten, false, "listen for connection");



function handle_connection($cskt, $csktaddr, $csktport) {
    printf("handle connection from [{$csktaddr}:{$csktport}]\n");
    // read from connection and print
    while(true) {
        $msg = socket_read($cskt, 2048);

        printf("recv msg from [{$csktaddr}:{$csktport}] {$msg}\n");

        if ($msg === 'Bye') {
            break;
        }
    }
    // colse connection socket
    socket_close($cskt);
    printf("close connection from [{$csktaddr}:{$csktport}]\n");
}

