<?php


function runAction($config) {
    if( getenv('DEV')) {
        echo "pong-dev";
    } else {
        echo "pong";
    }
    http_response_code(200);
}