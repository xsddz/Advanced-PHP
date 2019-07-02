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

// step2: connent to server
printf("connent to [{$ipaddress}:{$ipport}]\t.......\t");
$isconnent = socket_connect($skt, $ipaddress, $ipport);
if (false == $isconnent) {
    printf("\033[31m[FAIL]\033[0m\n");
    exit(0);
} else {
    printf("[OK]\n");
}

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
printf("close socket\t.......\t");
socket_close($skt);
printf("[OK]\n");

