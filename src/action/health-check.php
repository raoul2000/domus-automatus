<?php

require_once(__DIR__ . "/../lib/ftp-client/FtpClient.php");
require_once(__DIR__ . "/../lib/ftp-client/FtpException.php");
require_once(__DIR__ . "/../lib/ftp-client/FtpWrapper.php");


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
        Logger::error("health-check: " . $th->getMessage());
        $healtCheck['ftpConnection'] = false;
        $healtCheck['ftpConnectionError'] = $th->getMessage();
    }
    http_response_code(200);
    echo json_encode($healtCheck);
}
