<?php

require_once("../lib/ftp-client/FtpClient.php");
require_once("../lib/ftp-client/FtpException.php");
require_once("../lib/ftp-client/FtpWrapper.php");


function runAction($config)
{
    Logger::info("health-check : request");

    $healtCheck = array(
        'ftpConnection' => true,
        'ftp' => [
            'host' => $config['ftp']['host'],
            'username' => $config['ftp']['username'],
            'rootDir' => $config['ftp']['rootDir']
        ],
        'ts' => time()
    );

    try {

        $host = $config['ftp']['host'];
        $username = $config['ftp']['username'];
        $password = $config['ftp']['password'];
        
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($host);
        $ftp->login($username, $password);

        $ftp->scanDir($config['ftp']['rootDir']);

    } catch (\Throwable $th) {
        $healtCheck['ftpConnection'] = false;
        $healtCheck['ftpConnectionError'] = $th->getMessage();
    }
    echo json_encode($healtCheck);
    http_response_code(200);
}
