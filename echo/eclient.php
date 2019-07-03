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

// step2: connent to server
$isconnect = socket_connect($skt, $ipaddress, $ipport);
Yps_CommonUtils::failCheck($isconnect, false, "connent to [{$ipaddress}:{$ipport}]");

// send msg
while(true) {
    $msg = readline('> ');
    !empty($msg) && readline_add_history($msg);

    socket_send($skt, $msg."\n", strlen($msg."\n"), MSG_EOF);

    if ($msg == 'Bye') {
        break;
    }
}

// step3: close socket
socket_close($skt);
Yps_CommonUtils::failCheck(true, false, "close socket");

