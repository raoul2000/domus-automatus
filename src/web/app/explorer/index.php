<?php
if (getenv('DEV')) {
    require("../../../config/web.dev.php"); // dev config must not be committed (it contains secrets)
} else {
    require("../../../config/web.php");
}
date_default_timezone_set($config['timezone']);

$authKey =  $config['authorizationKey'];
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="../global.css" rel="stylesheet">
    <script type="text/javascript" src="explorer.js"></script>
    <style>
        .breadcrumbs {
            list-style: none;
            display: flex;
            gap: 0.2em;
            padding: 0;
        }

        .breadcrumbs li a {
            text-decoration: none;
        }

        .breadcrumbs li a:hover {
            text-decoration: underline;
        }

        .breadcrumbs li:not(:last-child)::after {
            content: " /";
        }

        #loading {
            padding-left: 0.5em;
            color: gray;
        }
        .hidden {
            visibility: hidden;
        }

        .file:hover,
        .dir:hover {
            cursor: pointer;
            background-color: rgb(255, 249, 133);
        }
    </style>
    <script>
        const key = "<?=  $authKey ?>";
    </script>
</head>

<body>
    <h1>Explorer<small id="loading">loading ...</small></h1>
    <div id="main">
        <p>loading...</p>
    </div>
</body>

</html>