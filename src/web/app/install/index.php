<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation ....</title>
    <link href="../global.css" rel="stylesheet">
</head>
<body>
    <h1>Installation</h1>
    <div id="main">
      <pre>
<?php

  echo "loading config...\n";
  if (getenv('DEV')) {
      require("../../../config/web.dev.php"); // dev config must not be committed (it contains secrets)
  } else {
      require("../../../config/web.php");
  }

  ///////////////////////////////////////////////////////////////////////////////
  // Protect Explorer with Basic HTTP Authentication
  // UPDATE USERNAMPE AND PASSWORD //////////////////////////////////////////////

  $USERNAME=trim($config['admin']['username']);
  $PASSWORD=trim($config['admin']['password']);

  if( strlen($USERNAME) == 0 ||  strlen($PASSWORD) == 0 ) {
    echo "ERROR : missing user credentials\n";
  } else {

    ///////////////////////////////////////////////////////////////////////////////

    $autPwdFilename= realpath('../../../config') . '/.htpasswd';
    $htaccessFilename= realpath('..') . '/.htaccess';

    if( ! file_exists($autPwdFilename)  ) {
      echo ".htpasswd file not found $autPwdFilename\n";
      echo "creating ...\n";

      touch($autPwdFilename);
      echo ".htpasswd file created\n";
      echo "writting password...\n";
      $clearTextPassword = $PASSWORD;

      // Encrypt password
      //$password = crypt($clearTextPassword, base64_encode($clearTextPassword));
      $password = base64_encode(sha1($clearTextPassword, true));
      file_put_contents($autPwdFilename, "$USERNAME:{SHA}$password");
      echo "password writen to .htpasswd file\n";
    } else {
      echo ".htpasswd file exist : $autPwdFilename (not modified)\n";
    }

    $htaccessContent="AuthName \"Page d'administration protégée\"\n"
    . "AuthType Basic\n"
    ."AuthUserFile $autPwdFilename\n"
    ."Require valid-user";

    echo "updating file $htaccessFilename...\n";
    file_put_contents($htaccessFilename,$htaccessContent);
    echo "file uopdated $htaccessFilename\n";

    echo "cleaning...\n";
    rename(__FILE__, __FILE__ . "_" .  date('Ymd_His'));
    echo "(weak) Protection enabled\n";
  }
?>
      </pre>
    </div>
</body>

</html>


