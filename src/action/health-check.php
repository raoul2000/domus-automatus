<?php

require_once("../lib/ftp-client/FtpClient.php");
require_once("../lib/ftp-client/FtpException.php");
require_once("../lib/ftp-client/FtpWrapper.php");


function runAction($config)
{
    $healtCheck = array(
        'ftpConnection' => true,
        'ts' => time()
    );

    try {
        
        $host = $config['ftp']['host'];
        $username = $config['ftp']['username'];
        $password = $config['ftp']['password'];
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($host);
        $ftp->login($username, $password); 

        //print_r( $ftp->scanDir('domoticus'));
    } catch (\Throwable $th) {
        $healtCheck['ftpConnection'] = false;
        $healtCheck['ftpConnectionError'] = $th->getMessage();
    }
    echo json_encode($healtCheck);
    http_response_code(200);
}
