
<?php
if (getenv('DEV')) {
    require("../../../config/web.dev.php"); // dev config must not be committed (it contains secrets)
} else {
    require("../../../config/web.php");
}
date_default_timezone_set($config['timezone']);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Log</title>
        <link href="../global.css" rel="stylesheet">
        <style>
            .nav-back {
                text-decoration: none;
            }
            .nav-back:hover {
                text-decoration: underline;
            }
            pre {
                word-wrap: break-word;
                white-space: pre-wrap;
                word-wrap: break-word;
                white-space: pre-wrap;
                padding: 1em;
                background-color: aliceblue;                
            }
            li {
                margin-bottom: 0.2em;
                font-size: 1.2em;
            }
            li a {
                padding:4px;
            }
            li a:hover {
                background-color: rgb(255, 249, 133);
            }
        </style>
    </head>
    <body>
        <div><a href="/app">Home</a> | <a href="/app/explorer/index.php">explorer</a></div>
        <?php
        if (isset($_REQUEST['file'])) {
            $logFilePath = $config['logDir'] . "/" . $_REQUEST['file'];
            if (file_exists($logFilePath) && is_file($logFilePath)) {  ?>
                <h1>log file : <?=$_REQUEST['file']?></h1>
                <p> <a class="nav-back" href="index.php">Log list</a></p>
                <pre><?= file_get_contents($logFilePath) ?></pre>
            <?php } else {
                echo "file not found";
            }
        } else { ?>
            <h1>Log File Index</h1>
            <?php
            $entries = array_diff(scandir($config['logDir']), array('.', '..'));
            $files = [];
            foreach ($entries as $f) {
                $path = $config['logDir'] . "/" . $f;
                if (is_file($path)) {
                    $files[$f] = filemtime($path);
                }
            }
            // sort by modification time, newest first
            arsort($files);
            echo "<ol>";
            $requestUri = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES);
            foreach (array_keys($files) as $logFile) {
                $display = htmlspecialchars($logFile, ENT_QUOTES);
                $linkFile = rawurlencode($logFile);
                echo "<li><a href='" . $requestUri . "?file=" . $linkFile . "'>" . $display . "</a></li>";
            }
            echo "</ol>";
        } ?>
    </body>
</html>


