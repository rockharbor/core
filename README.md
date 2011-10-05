# CORE

CORE is in-depth church member and event management software.

## Features

* User management, including household grouping, full profiles, address history
* Campus and ministry management
* Involvement management, including recurring dates, rosters
* Much more

## Requirements

* CakePHP 1.3.x (tested up to 8) in the same directory where core will reside,
  named 'cakephp'
  Example: 
    /var/www/core
    /var/www/cakephp

* MySQL Server

## Installation Steps

1. Install CORE by cloning it with git
    $ git clone --recursive git://codaset.com/rockharbor/core.git core

2. Rename the config/core.php.default to config/core.php
3. Change the salt value in config/core.php
4. Rename the config/database.php.default to config/database.php
5. Change the username/password/database values in database.php
6. Install CakePHP by cloning it with git
    $ git clone git://github.com/cakephp/cakephp.git cakephp
    $ git checkout 1.3.11

7. Make sure MySQL is running and create the database you configured in database.php
8. Add the directory where the Cake baker lives to your path (i.e., to the end of your ~/.bashrc file)
    export PATH=/var/www/cakephp/cake/console/:$PATH 

9. You should now be able to execute the command "cake" in any directory and get this output.

    Welcome to CakePHP v1.3.10 Console
    ---------------------------------------------------------------
    Current Paths
     -app: core
     -working: /var/www/core
     -root: /var/www
     -core: /var/www/cakephp

    Changing Paths:
    your working path should be the same as your application path
    to change your path use the '-app' param.
    Example: -app relative/path/to/myapp or -app /absolute/path/to/myapp

    Available Shells:
     acl [CORE]                        benchmark [DebugKit]              queue_sender [QueueEmail]         
     acl_extras [AclExtras]            console [CORE]                    schema [CORE]                     
     api [CORE]                        i18n [CORE]                       testsuite [CORE]                  
     api_index [ApiGenerator]          install [Install]                 whitespace [DebugKit]             
     asset_compress [AssetCompress]    media [Media]                     
     bake [CORE]                       migrator [Migrator]               

    To run a command, type 'cake shell_name [args]'
    To get help on a specific command, type 'cake shell_name help'

10. From the core directory /var/www/core/ execute 
    $ cake -app /var/www/core install install

11. Finally, initialize the media folders.
    $ cake -app /var/www/core media init

A default user 'admin' with the password 'password' will be created for you. Depending on your
setup, you may need to modify the `tmp` directory and the `webroot/media/transfer` directory
to be writeable by Apache.

When baking, make sure to pass the `-app` parameter to ensure you bake against CORE.

## Maintenance

CORE uses the rapid development framework [CakePHP][1] and is
currently maintained by ROCKHARBOR church in Costa Mesa, CA.

ROCKHARBOR - the church maintaining CORE
[http://www.rockharbor.org][2]

CORE is currently in a major refactoring process after being developed
originally in CakePHP 1.1 and not maintained, after which it will be released
as open source.

[1]: http://cakephp.org
[2]: http://rockharbor.org
