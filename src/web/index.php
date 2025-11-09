<?php

if (getenv('DEV')) {
    require("../config/web.dev.php"); // dev config mlust not be committed
} else {
    require("../config/web.php");
}

date_default_timezone_set($config['timezone']);

// Access Control ///////////////////////////////////////////////////////////////////////

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : "";
$authKey = array_key_exists('authKey', $_REQUEST) ? $_REQUEST['authKey'] : "";

if ($authKey !=  $config['authorizationKey']) {
    return http_response_code(401); // Unauthorized
}

// Action trigger ///////////////////////////////////////////////////////////////////////

switch ($action) {
    case 'ping':
        require('../action/ping.php');
        break;
    case 'send-sms':
        require('../action/send-sms.php');
        break;

    default:
        echo "missing or invalid action";
        return http_response_code(500);
        break;
}

// HTTP status code /////////////////////////////////////////////////////////////////////

$returnCode = runAction($config) ?? 200;
http_response_code($returnCode);
