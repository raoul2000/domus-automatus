<?php
echo "Creating ftp-deplyment configuration...";
echo "\n\n";

$deployIniTemplate =  __DIR__ . "/deploy.ini.template";

// validate command line argument
// The command line must contain the environment name to create
if( ! isset($argv[1])){
  echo "[ERROR] missing environment name\n";
  exit(1);
}

$envName = $argv[1];

echo "environment            : $envName\n";
echo "configuration template : $deployIniTemplate\n";

$envConfigFilename = __DIR__ . "/$envName.conf";
echo "configuration file     : $envConfigFilename\n";

if( ! file_exists($envConfigFilename)){
  echo "[ERROR] configuration file not found : $envConfigFilename\n";
  exit(1);
}

require_once($envConfigFilename);

echo "\nloading configuration file from :  $envConfigFilename\n";
if( !isset($env)){
  echo "[ERROR] incorrect env configuration in file $envConfigFilename\n";
  exit(1);
}

$config = file_get_contents($deployIniTemplate);
$config = strtr($config, $env);

$iniFilename = __DIR__ . "/$envName.ini";

echo "creating deployment ini file $iniFilename ... \n";

if( file_put_contents($iniFilename, $config) === FALSE) {
  echo "[ERROR] failed to create deployment ini file\n";
  exit(1);
}

echo "\n\nto deploy from the project folder, enter :\n\n";
echo "(win)     vendor\\bin\\deployment.bat deploy\\$envName.ini\n";
echo "(bash)    vendor/bin/deployment deploy/$envName.ini\n\n";
echo "add option -t to test the deployment:\n\n";
echo "(win)     vendor\\bin\\deployment.bat -t deploy\\$envName.ini\n";
echo "(bash)    vendor/bin/deployment -t deploy/$envName.ini\n\n";
echo "done\n";
