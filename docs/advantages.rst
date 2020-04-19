Advantages of PhpStratum
========================

In this chapter we discuss the advantages of using PhpStratum project (we will not discuss the advantages of using store procedures) over manually coding software for invoking stored procedures from your application.

Advantages of using PhpStratum (in no particular order) are:

* No boiler templating. Using PhpStratum relieves you from boiler templating your stored procedure code from dropping the stored procedure if it already exists or setting delimiters. Dropping or replacing the stored procedure or setting delimiters is done automatically by PhpStratum.

* PhpStratum generates wrapper methods automatically based on the metadata retrieved from the database. Hence, the wrapper will have the right number and the right types of arguments corresponding with the arguments of the stored procedure. Also, the return type of the wrapper is automatically determined based in the designation type of the stored procedure. Hence, when a stored procedure has been modified:

    * the number of arguments has changed,
    * the type of an argument has changed,
    * the designation type has changed.

  The wrapper for the stored procedure will be automatically changed to align with the stored procedure. When in your code the stored procedure is invoked with wrong number of arguments, an argument of the wrong type, or expecting a different return type, automated code inspection, IDE, or compiler will notify you about this mismatch.

  When you code manually software for invoking stored procedure, you might oversee the changed arguments or return type and will be unaware about this issue till someone runs your code.

* Saving time and cost. Wrappers for invoking stored procedures from your application are generated automatically saving you coding manually. Also, when you modify or add a stored procedure, the stored procedure will be loading into your database automatically.

* Automatically loading of stored procedures. Stored procedures are loaded into the database automatically by PhpStratum. Hence, you don't have to write scripts for loading stored procedures into your database. Both for your development database or the production database. Also, the correct version of the stored procedures are always loaded into the database, no stored procedure is overseen.

* Constants based on data and metadata stored in database. In your application you can use constants based on the metadata or data of your database.

* Consistent mapping between types in the programming language and database types.

* Invoking a stored procedure is just as simple as calling any other method of a class.

* No SQL code in your application code base.

* Improved security. Your application requires execution rights on stored procedures only. Tables, stored procedures and other database entities can be (highly recommended) owned by another account than the account your application is using for accessing the database.

* Automatically generated wrappers for invoking stored procedures will protect your application against SQL injection attacks.
