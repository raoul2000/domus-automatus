<?php

require_once(__DIR__ . "/../lib/ftp-client/FtpClient.php");
require_once(__DIR__ . "/../lib/ftp-client/FtpException.php");
require_once(__DIR__ . "/../lib/ftp-client/FtpWrapper.php");

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
function rawToType($permission)
{
    if (!is_string($permission)) {
        throw new Exception('The "$permission" argument must be a string, "'
            . gettype($permission) . '" given.');
    }

    if (empty($permission[0])) {
        return 'unknown';
    }

    switch ($permission[0]) {
        case '-':
            return 'file';

        case 'd':
            return 'directory';

        case 'l':
            return 'link';

        default:
            return 'unknown';
    }
}
function parseRawList(array $rawlist)
{
    $items = array();
    $path  = '';

    foreach ($rawlist as $key => $child) {
        $chunks = preg_split("/\s+/", $child, 9);

        if (isset($chunks[8]) && ($chunks[8] == '.' or $chunks[8] == '..')) {
            continue;
        }

        if (count($chunks) === 1) {
            $len = strlen($chunks[0]);

            if ($len && $chunks[0][$len - 1] == ':') {
                $path = substr($chunks[0], 0, -1);
            }

            continue;
        }

        // Prepare for filename that has space
        $nameSlices = array_slice($chunks, 8, true);

        $item = [
            'permissions' => $chunks[0],
            'number'      => $chunks[1],
            'owner'       => $chunks[2],
            'group'       => $chunks[3],
            'size'        => $chunks[4],
            'month'       => $chunks[5],
            'day'         => $chunks[6],
            'time'        => $chunks[7],
            'name'        => implode(' ', $nameSlices),
            'type'        => rawToType($chunks[0]),
        ];

        if ($item['type'] == 'link' && isset($chunks[10])) {
            $item['target'] = $chunks[10]; // 9 is "->"
        }

        // if the key is not the path, behavior of ftp_rawlist() PHP function
        if (is_int($key) || false === strpos($key, $item['name'])) {
            array_splice($chunks, 0, 8);

            $key = $item['type'] . '#'
                . ($path ? $path . '/' : '')
                . implode(' ', $chunks);

            if ($item['type'] == 'link') {
                // get the first part of 'link#the-link.ext -> /path/of/the/source.ext'
                $exp = explode(' ->', $key);
                $key = rtrim($exp[0]);
            }

            $items[$key] = $item;
        } else {
            // the key is the path, behavior of FtpClient::rawlist() method()
            $items[$key] = $item;
        }
    }

    return $items;
}

function ftp_ls($config, $dirToScan)
{
    $host = $config['ftp']['host'];
    $user = $config['ftp']['username'];
    $pass = $config['ftp']['password'];
    $rootDir = $config['ftp']['rootDir'];
    $port    = 21;
    $timeout = 30;
    $ssl     = false; //(array_key_exists('ssl', $_GET) && $_GET['ssl'] === '1');
    $active  = false; //(array_key_exists('active', $_GET) && $_GET['active'] === '1');

    if (empty($host)) {
        throw new Exception('Missing required parameter: host');
    }

    $conn = @ftp_connect($host, $port, $timeout);
    if (!$conn) {
        throw new Exception("Unable to connect to FTP host: {$host}:{$port}");
    }

    // Login
    if (!@ftp_login($conn, $user, $pass)) {
        throw new Exception("FTP login failed for user '{$user}'");
        @ftp_close($conn);
    }

    // Switch passive/active mode
    // If active=1 is requested we set passive mode off. default is passive (recommended behind NAT)
    @ftp_pasv($conn, !$active);

    @ftp_chdir($conn, $dirToScan);
    $listing = @ftp_rawlist($conn, '.', false);

    if ($listing === false) {
        throw new Exception("Failed to list directory or directory is empty");
    } else {
        $result = parseRawList($listing);
    }


    @ftp_close($conn);
    return $result;
}

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
            if (preg_match_all($re, $dirParam, $matches, PREG_SET_ORDER, 0)) {
                throw new Exception(('invalid character'));
            }
            $dirToScan = $rootDir . '/' . $dirParam;
        }

        Logger::info("ftp-ls : scanDir " . $dirToScan);

        /*$ftp = new \FtpClient\FtpClient();
        $ftp->connect($host);
        $ftp->pasv(true);
        $ftp->login($username, $password);

        echo json_encode($ftp->scanDir($dirToScan));
        */
        echo json_encode(ftp_ls($config, $dirToScan));
    } catch (\Throwable $th) {
        http_response_code(500);
        echo json_encode([
            'error' => 'failed to list folder',
            'cause' => $th->getMessage()
        ]);
        Logger::error("ftp-ls: operation failed : " . $th->getMessage());
    }
}
