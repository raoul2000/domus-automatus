<?php

require_once("../lib/ftp-client/FtpClient.php");
require_once("../lib/ftp-client/FtpException.php");
require_once("../lib/ftp-client/FtpWrapper.php");

/*

Dir item : 
```json
{
    "directory#domoticus\/cam1\/2025\/11\/16": {
        "permissions": "drwx------",
        "number": "2",
        "owner": "web",
        "group": "site",
        "size": "92",
        "month": "Nov",
        "day": "16",
        "time": "12:01",
        "name": "16",
        "type": "directory"
    }
}
```

File item : 
```json
{
    "file#domoticus\/cam1\/2025\/11\/21\/camera 1_00_20251121120806.jpg": {
        "permissions": "-rw-r--r--",
        "number": "1",
        "owner": "web",
        "group": "site",
        "size": "150408",
        "month": "Nov",
        "day": "21",
        "time": "11:08",
        "name": "camera 1_00_20251121120806.jpg",
        "type": "file"
        }
}
```
*/


function runAction($config)
{
    Logger::info("ftp-ls : request");

    $returnCode = 200;
    try {

        $host = $config['ftp']['host'];
        $username = $config['ftp']['username'];
        $password = $config['ftp']['password'];
        $rootDir = $config['ftp']['rootDir'];

        $dirToScan = $rootDir;
        $dirParam = array_key_exists('dir', $_REQUEST) ? trim($_REQUEST['dir']) : "";
        if (strlen($dirParam) !== 0) {
            $re = '/[\.\*\?]/m';
            if(preg_match_all($re, $dirParam, $matches, PREG_SET_ORDER, 0)) {
                throw new Exception(('invalid character'));
            }
            $dirToScan = $rootDir . '/' . $dirParam;
        }

        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($host);
        $ftp->login($username, $password);

        echo json_encode($ftp->scanDir($dirToScan));
    } catch (\Throwable $th) {
        echo json_encode([
            'error' => 'failed to list folder',
            'cause' => $th->getMessage()
        ]);
        $returnCode = 500;
    }
    //http_response_code($returnCode);
}
