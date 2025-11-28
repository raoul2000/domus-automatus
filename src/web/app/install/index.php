<?php

if (getenv('DEV')) {
    require("../../../config/web.dev.php"); // dev config must not be committed (it contains secrets)
} else {
    require("../../../config/web.php");
}

///////////////////////////////////////////////////////////////////////////////
// Protect Explorer with Basic HTTP Authentication
// UPDATE USERNAMPE AND PASSWORD //////////////////////////////////////////////

$USERNAME=$config['admin']['username'];
$PASSWORD=$config['admin']['password'];

///////////////////////////////////////////////////////////////////////////////

$autPwdFilename= realpath('../../../config') . '/.htpasswd';
$htaccessFilename= realpath('..') . '/.htaccess';

if( ! file_exists($autPwdFilename)  ) {
  echo ".htpasswd file not found $autPwdFilename: creating ...<br/>";
  echo "creating ...<br/>";
  
  touch($autPwdFilename);
  $clearTextPassword = $PASSWORD;

  // Encrypt password
  //$password = crypt($clearTextPassword, base64_encode($clearTextPassword));
  $password = base64_encode(sha1($clearTextPassword, true));
  file_put_contents($autPwdFilename, "$USERNAME:{SHA}$password");
} else {
  echo ".htpasswd file exist : $autPwdFilename (not modified)<br/>";
}

  $htaccessContent="AuthName \"Page d'administration protégée\"\n"
  . "AuthType Basic\n"
  ."AuthUserFile $autPwdFilename\n"
  ."Require valid-user";

  echo "updating file $htaccessFilename<br/>";
  file_put_contents($htaccessFilename,$htaccessContent);
  if( unlink(__FILE__) === false){
    echo "ERROR : failed to delete file " . __FILE__. "<br/>";
  } else {
    echo "deleting deploy folder<br/>";
    rmdir(__DIR__);
  }
  echo "(weak) Protection enabled<br/>";
