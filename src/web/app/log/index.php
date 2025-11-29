
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
            }
        </style>
    </head>
    <body>
        <?php
        if (isset($_REQUEST['file'])) {
            $logFilePath = $config['logDir'] . "/" . $_REQUEST['file'];
            if (file_exists($logFilePath) && is_file($logFilePath)) {  ?>
                <h1>log file : <?=$_REQUEST['file']?></h1>
                <p> <a class="nav-back" href="index.php">&lt;&lt; to index</a></p>
                <pre><?= file_get_contents($logFilePath) ?></pre>
            <?php } else {
                echo "file not found";
            }
        } else { ?>
            <h1>Log File Index</h1>
            <?php
            $files = array_diff(scandir($config['logDir']), array('.', '..'));
            echo "<ul>";
            foreach ($files as $logFile) {
                echo "<li><a href='".$_SERVER['REQUEST_URI']."?file=".$logFile."'>" . $logFile . "</a></li>";
            }
            echo "</ul>";
        } ?>
    </body>
</html>


