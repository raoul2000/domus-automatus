# Overview

## Installation

### Requirements

Requirements :
- php >= 8.0
- composer

```
git clone https://github.com/raoul2000/domus-automatus.git
cd domus-automatus
composer install
```

NOTE : the *composer* package installed is [dg/ftp-deployment](https://github.com/dg/ftp-deployment) which is only needed to deploy automatically the project
on an FTP folder (see next chapter).

### Deploy FTP

FTP deployment is managed by [ftp deployment](https://github.com/dg/ftp-deployment) which is included
in the *composer* dependencies.

To setup a *stage* deployment process :

- duplicate the environment configuration file :
```
cp deploy\environment.conf.example deploy\stage.conf
```
- update your `deploy\stage.conf` file
- generate the deployment scripts with `php deploy\create.php stage`
- start deployment with `vendor/bin/deployment - deploy/prod.ini`

You can also run a deployement in simulation (test) mode :

```
vendor/bin/deployment -t deploy/prod.ini
```
