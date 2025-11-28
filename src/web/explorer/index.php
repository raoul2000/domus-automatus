<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript" src="explorer.js"></script>
    <style>
        body {
            font-family: sans-serif;
            margin: 1em;
            padding: 1em;
        }
        h1 {
            border-bottom: 1px solid #cbcbcb;
            padding-bottom: 0.5em;
        }
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
</head>

<body>
    <h1>Explorer<small id="loading">loading ...</small></h1>
    <div id="main">
        <p>loading...</p>
    </div>
</body>

</html>