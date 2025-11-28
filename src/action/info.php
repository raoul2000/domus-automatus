<?php
/**
 * Returns information about the App
 */
function runAction($config)
{
    header('Content-Type: application/json');

    /*
    Example : JSON response from associative array

    $jsonData = array(
        'organization' => 'GeeksforGeeks',
        'founder' => 'Sandeep Jain',
        'employee' => 'Gaurav',
        'ts' => time()
    );

    echo json_encode($jsonData);
     */

    //echo file_get_contents("../../version.json");
    
    echo file_get_contents(__DIR__ . "/../version.json");
    http_response_code(200);
}
