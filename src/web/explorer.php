<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

        .hidden {
            visibility: hidden;
        }

        .file:hover {
            cursor: pointer;
            background-color: rgb(255, 249, 133);
        }
    </style>
</head>

<body>
    <h1>Explorer<small id="loading">loading ...</small></h1>
    <hr />
    <div id="main">
        <p>sdfgsdfg</p>
    </div>
</body>

</html>