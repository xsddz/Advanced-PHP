<?php

$ipaddress = '127.0.0.1';
$ipport = '8099';

// step1: create a socket
printf("create a socket\t.......\t");
$skt = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (false === $skt) {
    printf("\033[31m[FAIL]\033[0m\n");
    exit(0);
} else {
    printf("[OK]\n");
}

// step2: bind to ip:port
printf("bind to [{$ipaddress}:{$ipport}]\t.......\t");
$isbind = socket_bind($skt, $ipaddress, $ipport);
if (false === $isbind) {
    printf("\033[31m[FAIL]\033[0m\n");
    exit(0);
} else {
    printf("[OK]\n");
}

// step3: listen for connection
printf("listen for connection\t.......\t");
$islisten = socket_listen($skt, 1024);
if (false === $islisten) {
    printf("\033[31m[FAIL]\033[0m\n");
    exit(0);
} else {
    printf("[OK]\n");
}

// step4: access connection and handle
printf("\n");
while(true) {
    $cskt = socket_accept($skt);
    if (false === $cskt) {
        printf("access connection error\n");
    } else {
        socket_getpeername($cskt, $csktaddr, $csktport);
        printf("accept connection from [{$csktaddr}:{$csktport}]\n");
        handle_connection($cskt, $csktaddr, $csktport);
    }
}
printf("\n");

// step5: close socket
printf("close socket\t.......\t");
socket_close($skt);
printf("[OK]\n");



function handle_connection($cskt, $csktaddr, $csktport) {
    printf("handle connection from [{$csktaddr}:{$csktport}]\n");
    while(true) {
        // read a line from connection
        $msg = skt_recv_line($cskt);
        if (!empty($msg)) {
            printf("recv msg from [{$csktaddr}:{$csktport}] {$msg}\n");
        }

        if ($msg === 'Bye') {
            break;
        }
    }

    // colse connection socket
    socket_close($cskt);
    printf("close connection from [{$csktaddr}:{$csktport}]\n");
}

function skt_recv_line($cskt) {
    $line = "";
    while(true) {
        // read a char
        $res = socket_recv($cskt, $buf, 1, MSG_WAITALL);
        if ($buf == "\n") {
            break;
        } else {
            $line .= $buf;
        }
    }
    return $line;
}

