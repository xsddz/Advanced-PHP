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

    handle_connection($cskt);
}

// step5: close socket
socket_close($skt);
Yps_CommonUtils::failCheck($islisten, false, "listen for connection");



function handle_connection($cskt) {
    // get connection info
    socket_getpeername($cskt, $csktaddr, $csktport);
    printf("handle connection from [{$csktaddr}:{$csktport}]\n");

    // read request
    $request = read_http_request($cskt);
    // printf("recv msg from [{$csktaddr}:{$csktport}]\n{$request}\n");

    // prase request
    $requestInfo = parse_http_request($request);
    // to do ...
    //

    // send response
    $response = build_http_response(200, make_request_html_string($requestInfo));
    socket_write($cskt, $response, strlen($response));

    // colse connection
    socket_close($cskt);
    printf("close connection from [{$csktaddr}:{$csktport}]\n\n");
}

function read_http_request($skt) {
    $request = "";
    while(true) {
        $buf = socket_read($skt, 2048);
        $request .= $buf;

        if (strlen($buf) < 2048) {
            break;
        }
    }
    return $request;
}

function parse_http_request(&$request) {
    $info = array();
    for($si=$ei=0; true; $ei++) {
        if (($request[$ei] === "\r") && ($request[$ei+1] === "\n")) {
            if ($si === $ei) {
                $info['request_body'] = substr($request, $ei+2);
                break;
            }

            $line = substr($request, $si, $ei-$si);
            if ($si === 0) {
                $requestLineInfo = explode(' ', $line);
                $info['request_method'] = $requestLineInfo['0'];
                $info['request_uri'] = $requestLineInfo['1'];
                $info['request_version'] = $requestLineInfo['2'];
            } else {
                $seppos = strpos($line, ':');
                $headerName = substr($line, 0, $seppos);
                $headerData = substr($line, $seppos+1);
                $info['request_header'][$headerName] = trim($headerData);
            }

            $si = $ei+2;
        }
    }
    return $info;
}

function build_http_response($statusCode, $data) {
    // make body
    $body = sprintf("<html><head><title>Tiny Web Server</title></head><body>%s</body></html>", $data);

    // make response
    $response = "";
    $response .= sprintf("%s %s %s\r\n", "HTTP/1.1", $statusCode, get_status_msg($statusCode));
    $response .= sprintf("content-type: %s\r\n", "text/html");
    $response .= sprintf("content-length: %s\t\n", strlen($body));
    $response .= sprintf("\r\n");
    $response .= sprintf("%s", $body);

    return $response;
}

function make_request_html_string(&$requestInfo) {
    $requestHtmlStr = "<pre>";
    $requestHtmlStr .= sprintf("request_method: %s\n", $requestInfo['request_method']);
    $requestHtmlStr .= sprintf("request_uri: %s\n", $requestInfo['request_uri']);
    $requestHtmlStr .= sprintf("request_version: %s\n", $requestInfo['request_version']);
    foreach($requestInfo['request_header'] as $rhn => $rhd) {
        $requestHtmlStr .= sprintf("%s: %s\n", $rhn, $rhd);
    }
    $requestHtmlStr .= sprintf("request_body: %s\n", $requestInfo['request_body']);
    $requestHtmlStr .= "</pre>";

    return $requestHtmlStr;
}

function get_status_msg($statusCode) {
    return "OK";
}

