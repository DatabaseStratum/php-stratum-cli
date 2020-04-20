Welcome to PhpStratum's documentation!
======================================

PhpStratum is a set of PHP packages for loading stored procedures into your application's database and invoking those stored procedures conveniently from your application using an automatically generated wrapper class. PhpStratum is available for the following database management systems:

* `MySQL and MariaDB`_
* `SQLite`_

.. _further-reading:

Further Reading
---------------

You have the following options for further reading:

* Continue reading this documentation.

* Reading a broader documentation about the concepts of `Stratum Projects`_ in other programming languages than PHP.

* Reading the documentation of PhpStratum for `MySQL and MariaDB`_. This documentation is self contained and doesn't require any knowledge from any other documentation.

* Reading the documentation of PhpStratum for `SQLite`_. This documentation is self contained and doesn't require any knowledge from any other documentation.

.. _Stratum Projects: https://stratum.readthedocs.io/
.. _MySQL and MariaDB: https://phpstratum-mysql.readthedocs.io/
.. _SQLite: https://phpstratum-sqlite-pdo.readthedocs.io/

.. _package-overview:

Package Overview
----------------

In this section we discuss how the packages of PhpStratum are organized.

The packages of PhpStratum are organized such that coupling between your application and the components of PhpStratum is kept at a minimum. Currently, the following PhpStratum packages are available:

+------------------------------------+-----------------------------------------------------------+
| Package                            |  Repository                                               |
+====================================+===========================================================+
| `setbased/php-stratum`_            | https://github.com/DatabaseStratum/php-stratum-cli        |
+------------------------------------+-----------------------------------------------------------+
| `setbased/php-stratum-mysql`_      | https://github.com/DatabaseStratum/php-stratum-mysql      |
+------------------------------------+-----------------------------------------------------------+
| `setbased/php-stratum-sqlite-pdo`_ | https://github.com/DatabaseStratum/php-stratum-sqlite-pdo |
+------------------------------------+-----------------------------------------------------------+
| `setbased/php-stratum-middle`_     | https://github.com/DatabaseStratum/php-stratum-middle     |
+------------------------------------+-----------------------------------------------------------+
| `setbased/php-stratum-backend`_    | https://github.com/DatabaseStratum/php-stratum-backend    |
+------------------------------------+-----------------------------------------------------------+
| `setbased/php-stratum-common`_     | https://github.com/DatabaseStratum/php-stratum-common     |
+------------------------------------+-----------------------------------------------------------+

.. _setbased/php-stratum-backend: https://packagist.org/packages/setbased/php-stratum-backend
.. _setbased/php-stratum-middle: https://packagist.org/packages/setbased/php-stratum-middle
.. _setbased/php-stratum-common: https://packagist.org/packages/setbased/php-stratum-common
.. _setbased/php-stratum-sqlite-pdo: https://packagist.org/packages/setbased/php-stratum-sqlite-pdo
.. _setbased/php-stratum-mysql: https://packagist.org/packages/setbased/php-stratum-mysql
.. _setbased/php-stratum: https://packagist.org/packages/setbased/php-stratum-cli

We discuss each package briefly bellow:

  setbased/php-stratum
    This package is the frontend of PhpStratum. It provides the command line interface (CLI) of PhpStratum. The CLI is documented in the documentation of each backend package.

  setbased/php-stratum-mysql
    This package is the MySQL and MariaDB backend of PhpStratum. The full documentation of the MySQL and MariaDB backend is available at https://phpstratum-mysql.readthedocs.io/.

  setbased/php-stratum-sqlite-pdo
    This package is the SQLite backend using PDO of PhpStratum. The full documentation of the SQLite backend using PDO is available at https://phpstratum-sqlite-pdo.readthedocs.io/.

  setbased/php-stratum-middle
    This package is the glue between your application and the backend package.

  setbased/php-stratum-backend
    This package is the glue between the frontend package and the backend packages of PhpStratum. You should not use this package directly unless you are developing a backend package.

  setbased/php-stratum-common
    This package contains code that is common between the backend packages of PhpStratum. You should not use this package directly unless you are developing a backend package.
