# PhpStratum
A stored procedure and function loader, wrapper generator for MySQL in PHP.

[![Build Status](https://travis-ci.org/SetBased/php-stratum.svg?branch=master)](https://travis-ci.org/SetBased/php-stratum)
[![Latest Stable Version](https://poser.pugx.org/setbased/php-stratum/v/stable)](https://packagist.org/packages/setbased/php-stratum)
[![Total Downloads](https://poser.pugx.org/setbased/php-stratum/downloads)](https://packagist.org/packages/setbased/php-stratum)
[![License](https://poser.pugx.org/setbased/php-stratum/license)](https://packagist.org/packages/setbased/php-stratum)

# Overview
PhpStratum is a tool and library with the following mayor functionalities:  
* Loading modified and new stored routines and removing obsolete stored routines into/from a MySQL instance. This MySQL instance can be part of your development or a production environment. 
* Enhancing the (limited) syntax of MySQL stored routines with constants and custom types (based on actual table columns).
* Generating automatically a PHP wrapper class for calling your stored routines. This wrapper class takes care about error handing and prevents SQL injections.
* Defining PHP constants based on auto increment columns and column widths.

# Example
We give an example based on a very simple database model that, nevertheless, will give you a good impression what PhpStratum does.

## Database 
The database model is shown in the image below. It consists of a table for user accounts and a table for the status of each user account.
![Database Model](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-model.png)
Table BAR_USER_STATUS is a reference table with static data that holds the following rows:

| ust_id | ust_description | ust_label |
| ------ | --------------- | --------- |
| 1 | Active  | BAR_UST_ID_ACTIVE |
| 2 | Blocked | BAR_UST_ID_BLOCKED |
| 3 | Retired | BAR_UST_ID_RETIRED |
 
## Stored Routines
For the web application we have two stored procedures:  
* A stored procedure for testing the availability of an account name.
* A stored procedure for retrieving all blocked accounts.

The sources of both stored procedures are shown in the two images below.
![bar_user_is_user_name_available](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-routine01.png)
![bar_user_get_blocked_accounts](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-routine02.png)

## Running PhpStratum
Running PhpStratum is straight forward. From the command line start stratum with a configuration file. 
![running_stratum](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-stratum.png)

## Results 
The results of running PhpStratum are fourfold. First of all the stored routines are loaded into the MySQL instance. 
Inspecting the stored routines (with Toad for MySQL) in the MySQL instance shows the second effect of PhpStratum.    
![bar_user_is_user_name_available](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-routine01-loaded.png)
![bar_user_get_blocked_accounts](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-routine02-loaded.png)

In stored procedure `bar_user_is_user_name_available` the parameter type `@bar_user.usr_name%type@` is replaced with the actual data type `varchar(20) character set utf8` and
in stored procedure `bar_user_get_blocked_accounts` placeholder `@BAR_UST_ID_BLOCKED@` is replaced with the actual value of `usr_id` for status blocked, i.e. `2`.

Thirdly, PhpStratum has automatically generated a wrapper class for calling the stored procedures from your PHP code.
This wrapper class takes care about error handing and prevents SQL injections. Also, this class includes 
[DocBlocks](http://phpdoc.org/docs/latest/glossary.html#term-docblock) based on the
[DocBlocks](http://phpdoc.org/docs/latest/glossary.html#term-docblock) in the source files of the stored procedures and 
the data types of the arguments of the stored procedures.
![DataLayer](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-datalayer.png)

Finally, PhpStratum has generated PHP code for defining constants.  
![config](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-config.png)  
The constants based on the length/size of the data type of a column, especially varchar columns, can be used for setting 
the size of an input element in a form. A rudimentary example of creating an input element for the user name in a login 
form is shown in the below.         
![sample_usage_constant](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-constant.png)  
This example generates an input element with length 20 in which the end-user cannot enter more than 20 characters.  
![sample_usage_constant_html1](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-constant-html1.png)  
Suppose new requirements of your web application state the maximum length of user names is 40 characters. With 
PhpStratum you need the modify column `usr_name` and rerun `stratum`.  
![alter_table](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-alter-table.png)  
![rerun_stratum](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-rerun-stratum.png)  
The above PHP statement yields now:  
![sample_usage_constant_html2](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-constant-html2.png)

# Advantages
* Easy loading of new and modified stored routines and removing of obsolete stored routines. We use PhpStratum on our
  development environments and also on the production environments of our customers. Actually, we use PhpStratum
  in %post scripts inside RPM distributions. 
* Automatic generation of a wrapper class with methods for each stored routine. This wrapper class protected you
  against SQL injection and takes care about error handling. There is no need to create painstakingly SQL statements
  yourself in your code for calling stored routines. Any change of the parameters of a stored routine will be reflected 
  in the wrapper class. With a modern IDE you can find each usage of each stored routine easily.
* There is no need to clutter the sources of your stored routines with `drop procedure ...` and `delimiter $$` 
  statements. Moreover, since the sources of your stored routines hold in effect one SQL statement only, when MySQL 
  finds a syntax error in the source of your stored routine the line number in MySQL's error message will correspond
  with the line number in the source file. 
* Modifications of the data type of table columns are automatically incorporated in the parameters and local variables 
  of your stored routines and in your PHP code. 
  For example:
  * Changing column `usr_id` from `small int` to `int unsigned`.
  * Changing column `fct_amount` from `float` to `double`.
  * Changing column `usr_name` from `varchar(20) character set latin1` to `varchar(40) character set utf8`. In this 
    case all the length of all input elements for user name is set to 40 as well.

# Installation
We recommend to install PhpStratum via [Composer](https://getcomposer.org/):  
```json
{
  "require": {
		"setbased/php-stratum": "3.*"
	}
}
```

# Further Reading
We are aiming to have the full documentation available in 2015 at GitHup Pages.
   
Examples are available at [PhpStratum Samples](https://github.com/SetBased/php-stratum-samples).    

# History of PhpStratum
## Version 1.x
The initial development of PhpStratum started in 2005 when MySQL 5.0 became GA. MySQL 5.0 was the first release with
stored routines. The first project were we used PhpStratum is the [Nahouw](https://www.nahouw.net). Currently, this
project has over 600 stored routines.

## Version 2.x
The development of PhpStratum 2.0 started at the end of 2010 when MySQL 5.5 became GA. MySQL 5.5 was the first release with
`information_schema.PARAMETERS`. The availability of `information_schema.PARAMETERS` provides metadata about the 
parameters and return type of stored routines.
  
## Version 3.x
The development of PhpStratum 3.0 started in 2013. The aim of this release is to make PhpStratum publicly available 
under the MIT licence and installable via [Composer](https://getcomposer.org/).

# Maturity
We have put more than 10 years of experience with stored routines into PhpStratum and have used PhpStratum in dozens
projects for our customers. Many of these projects have hundreds of stored routines.

# Sister Project 
We are also working on [PyStratum](https://github.com/SetBased/py-stratum).
[PyStratum](https://github.com/SetBased/py-stratum) provides the same functionalities as PhpStratum but in a Python 
environment and supports MySQL as well as Microsoft SQL Server.
