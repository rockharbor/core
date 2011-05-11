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
* MySQL

## Installation

To install, you currently need some knowledge in Git and CakePHP's baking shell.
A web installer is planned for the future.

First, install CORE by cloning it with git

    $ git clone --recursive git://codaset.com/rockharbor/core.git core

Next, rename the config/core.php.default and config/database.php.default to remove
'.default'. Change the username/password/database values in database.php. Change
the salt value in core.php as well.

Now we'll install the database and the default records, including groups, ACL, and
more.

    $ cake install install

A default user 'admin' with the password 'password' will be created for you.

Finally, initialize the media folders.

    $ cake media init

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