<?php
// This is the entry point : all request must enter here !


/**
 * Checks that the request includes the required authentication key. return HTTP / 401
 * if it's not the case.
 * 
 * The authentication key must be provided in `x-authKey` custom header value
 * 
 * Use this function before executing any protected action.
 */
function checkAuthenticationKey($config) {
    $authKey = array_key_exists('HTTP_X_AUTHKEY', $_SERVER) ? $_SERVER['HTTP_X_AUTHKEY'] : "";

    if ($authKey !=  $config['authorizationKey']) {
        Logger::error("unauthorized access", $_SERVER);
        return http_response_code(401); // Unauthorized
    }
}

// main ////////////////////////////////////////////////////////////////////////////////////////////

if (getenv('DEV')) {
    require("../config/web.dev.php"); // dev config must not be committed (it contains secrets)
} else {
    require("../config/web.php");
}
date_default_timezone_set($config['timezone']);

// setup log 
require('../lib/Logger.php');
Logger::$log_dir = $config['logDir'];


// Action trigger ///////////////////////////////////////////////////////////////////////

$action = array_key_exists('action', $_REQUEST) ? $_REQUEST['action'] : "";

switch ($action) {
    case 'ping':
        checkAuthenticationKey($config);
        require('../action/ping.php');
        break;
    case 'send-sms':
        checkAuthenticationKey($config);
        require('../action/send-sms.php');
        break;
    case 'health-check':
        checkAuthenticationKey($config);
        require('../action/health-check.php');
        break;
    case 'log':
        require('../action/log.php');
        break;
    case 'info':
        require('../action/info.php');
        break;
    default:
        Logger::error("missing or invalid action", $_SERVER);
        return http_response_code(500);
        break;
}

// HTTP status code /////////////////////////////////////////////////////////////////////

runAction($config);