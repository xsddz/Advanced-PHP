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

// step4: access connection
printf("\n");
while(true) {
    $res = socket_accept($skt);
    if (false === $res) {
        printf("access connection error\n");
    } else {
        handle_connection($res);
    }
}
printf("\n");

// step5: close socket
printf("close socket\t.......\t");
socket_close($skt);
printf("[OK]\n");


function handle_connection($skt) {
    socket_getpeername($skt, $sktaddr, $sktport);
    printf("handle connection from [{$sktaddr}:{$sktport}]\n");

    $msg = "";
    while($res = socket_recv($skt, $buf, 1, MSG_WAITALL)) {
        if ($buf !== "\n") {
            $msg .= $buf;
            continue;
        }

        if (!empty($msg)) {
            printf("recv msg from [{$sktaddr}:{$sktport}] {$msg}\n");
            $msg = "";
        }

        if ($msg === 'Bye') {
            break;
        }
    }
}

