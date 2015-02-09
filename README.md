# PhpStratum
A stored procedure and function loader, wrapper generator and more for MySQL in PHP.

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

The source of both stored procedures are show in the two images below.
![bar_user_is_user_name_available](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-routine01.png)
![bar_user_get_blocked_accounts](https://raw.githubusercontent.com/SetBased/php-stratum/gh-pages/images/samples/sample01-routine02.png)

## Running PhpStratum
Running PhpStratum is straight forward ... * will finisch this document by next week ... * 


 
## Results 




# Installation
We recommend to install PhpStratum via [Composer](https://getcomposer.org/):

```json
{
  "require-dev": {
		"setbased/php-stratum": "1.*"
	}
}
```
