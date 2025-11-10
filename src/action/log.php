<?php

function runAction($config)
{
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Log</title>
    </head>

    <body>
        <?php

        if (isset($_REQUEST['file'])) {
            $logFilePath = $config['logDir'] . "/" . $_REQUEST['file'];
            if (file_exists($logFilePath) && is_file($logFilePath)) {
        ?>
            <h1><?=$_REQUEST['file']?></h1>
            <hr/>
            <pre><?= file_get_contents($logFilePath) ?></pre>
        <?php
            } else {
                echo "file not found";
            }
        } else {
            $files = array_diff(scandir($config['logDir']), array('.', '..'));
            echo "<ul>";
            foreach ($files as $logFile) {
                echo "<li><a href='".$_SERVER['REQUEST_URI']."&file=".$logFile."'>" . $logFile . "</a></li>";
            }
            echo "</ul>";
        } ?>
    </body>

    </html>
<?php
}
