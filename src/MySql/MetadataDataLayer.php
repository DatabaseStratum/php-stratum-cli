<?php

namespace SetBased\Stratum\MySql;

use SetBased\Stratum\Style\StratumStyle;

/**
 * Data layer for retrieving metadata and loading stored routines.
 */
class MetadataDataLayer
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The connection to the MySQL instance.
   *
   * @var StaticDataLayer
   */
  private static $dl;

  /**
   * The Output decorator.
   *
   * @var StratumStyle
   */
  private static $io;

  //--------------------------------------------------------------------------------------------------------------------

  /**
   * Class a stored procedure without arguments.
   *
   * @param string $procedureName The name of the procedure.
   */
  public static function callProcedure(string $procedureName): void
  {
    $query = 'call '.$procedureName.'()';

    self::$dl->executeNone($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Checks if a table exists in the current schema.
   *
   * @param string $tableName The name of the table.
   *
   * @return bool
   */
  public static function checkTableExists(string $tableName): bool
  {
    $query = sprintf('
select 1
from   information_schema.TABLES
where table_schema = database()
and   table_name   = %s', self::$dl->quoteString($tableName));

    return !empty(self::executeSingleton0($query));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Connects to a MySQL instance.
   *
   * Wrapper around [mysqli::__construct](http://php.net/manual/mysqli.construct.php), however on failure an exception
   * is thrown.
   *
   * @param string $host     The hostname.
   * @param string $user     The MySQL user name.
   * @param string $passWord The password.
   * @param string $database The default database.
   * @param int    $port     The port number.
   */
  public static function connect(string $host, string $user, string $passWord, string $database, int $port = 3306): void
  {
    self::$dl = new StaticDataLayer();

    self::$dl->connect($host, $user, $passWord, $database, $port);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Describes a table.
   *
   * @param string $tableName The table name.
   *
   * @return array[]
   */
  public static function describeTable(string $tableName): array
  {
    $query = sprintf('describe `%s`', $tableName);

    return self::executeRows($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Closes the connection to the MySQL instance, if connected.
   */
  public static function disconnect(): void
  {
    if (self::$dl!==null)
    {
      self::$dl->disconnect();
      self::$dl = null;
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Drops a routine if it exists.
   *
   * @param string $routineType The type of the routine (function of procedure).
   * @param string $routineName The name of the routine.
   */
  public static function dropRoutine(string $routineType, string $routineName): void
  {
    $query = sprintf('drop %s if exists `%s`', $routineType, $routineName);

    self::executeNone($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Drops a temporary table.
   *
   * @param string $tableName the name of the temporary table.
   */
  public static function dropTemporaryTable(string $tableName): void
  {
    $query = sprintf('drop temporary table `%s`', $tableName);

    self::executeNone($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @param string $query The SQL statement.
   *
   * @return int The number of affected rows (if any).
   */
  public static function executeNone(string $query): int
  {
    self::logQuery($query);

    return self::$dl->executeNone($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 0 or 1 row.
   * Throws an exception if the query selects 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return array|null The selected row.
   */
  public static function executeRow0(string $query): ?array
  {
    self::logQuery($query);

    return self::$dl->executeRow0($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 1 and only 1 row.
   * Throws an exception if the query selects none, 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return array The selected row.
   */
  public static function executeRow1(string $query): array
  {
    self::logQuery($query);

    return self::$dl->executeRow1($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 0 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return array[]
   */
  public static function executeRows(string $query): array
  {
    self::logQuery($query);

    return self::$dl->executeRows($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 0 or 1 row.
   * Throws an exception if the query selects 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return mixed The selected row.
   */
  public static function executeSingleton0(string $query)
  {
    self::logQuery($query);

    return self::$dl->executeSingleton0($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Executes a query that returns 1 and only 1 row with 1 column.
   * Throws an exception if the query selects none, 2 or more rows.
   *
   * @param string $query The SQL statement.
   *
   * @return mixed The selected row.
   */
  public static function executeSingleton1(string $query)
  {
    self::logQuery($query);

    return self::$dl->executeSingleton1($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects metadata of all columns of all tables.
   *
   * @return array[]
   */
  public static function getAllTableColumns(): array
  {
    $query = "
(
  select table_name
  ,      column_name
  ,      column_type
  ,      data_type
  ,      character_maximum_length
  ,      numeric_precision
  from   information_schema.COLUMNS
  where  table_schema = database()
  and    table_name  rlike '^[a-zA-Z0-9_]*$'
  and    column_name rlike '^[a-zA-Z0-9_]*$'
  order by table_name
  ,        ordinal_position
)

union all

(
  select concat(table_schema,'.',table_name) table_name
  ,      column_name
  ,      column_type
  ,      data_type
  ,      character_maximum_length
  ,      numeric_precision
  from   information_schema.COLUMNS
  where  table_name  rlike '^[a-zA-Z0-9_]*$'
  and    column_name rlike '^[a-zA-Z0-9_]*$'
  order by table_schema
  ,        table_name
  ,        ordinal_position
)
";

    return self::executeRows($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects the SQL mode in the order as preferred by MySQL.
   *
   * @param string $sqlMode The SQL mode.
   *
   * @return string
   */
  public static function getCorrectSqlMode(string $sqlMode): string
  {
    $query = sprintf('set sql_mode = %s', self::$dl->quoteString($sqlMode));
    self::executeNone($query);

    $query = 'select @@sql_mode';

    return (string)self::executeSingleton1($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects metadata of tables with a label column.
   *
   * @return array[]
   */
  public static function getLabelTables(): array
  {
    $query = "
select t1.table_name  table_name
,      t1.column_name id
,      t2.column_name label
from       information_schema.columns t1
inner join information_schema.columns t2 on t1.table_name = t2.table_name
where t1.table_schema = database()
and   t1.extra        = 'auto_increment'
and   t2.table_schema = database()
and   t2.column_name like '%%\\_label'";

    return self::executeRows($query);
  }
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects all labels from a table with labels.
   *
   * @param string $tableName       The table name.
   * @param string $idColumnName    The name of the auto increment column.
   * @param string $labelColumnName The name of the column with labels.
   *
   * @return array[]
   */
  public static function getLabelsFromTable(string $tableName, string $idColumnName, string $labelColumnName): array
  {
    $query = "
select `%s`  id
,      `%s`  label
from   `%s`
where   nullif(`%s`,'') is not null";

    $query = sprintf($query, $idColumnName, $labelColumnName, $tableName, $labelColumnName);

    return self::executeRows($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects the parameters of a stored routine.
   *
   * @param string $routineName The name of the routine.
   *
   * @return array[]
   */
  public static function getRoutineParameters(string $routineName): array
  {
    $query = sprintf("
select t2.parameter_name
,      t2.data_type
,      t2.numeric_precision
,      t2.numeric_scale
,      t2.character_set_name
,      t2.collation_name
,      t2.dtd_identifier
from            information_schema.ROUTINES   t1
left outer join information_schema.PARAMETERS t2  on  t2.specific_schema = t1.routine_schema and
                                                      t2.specific_name   = t1.routine_name and
                                                      t2.parameter_mode   is not null
where t1.routine_schema = database()
and   t1.routine_name   = '%s'", $routineName);

    return self::executeRows($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects all routines in the current schema.
   *
   * @return array[]
   */
  public static function getRoutines(): array
  {
    $query = '
select routine_name
,      routine_type
,      sql_mode
,      character_set_client
,      collation_connection
from  information_schema.ROUTINES
where ROUTINE_SCHEMA = database()
order by routine_name';

    return self::executeRows($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects metadata of all columns of table.
   *
   * @param string $schemaName The name of the table schema.
   * @param string $tableName  The name of the table.
   *
   * @return array[]
   */
  public static function getTableColumns(string $schemaName, string $tableName): array
  {
    $sql = sprintf('
select COLUMN_NAME        as column_name
,      COLUMN_TYPE        as column_type
,      IS_NULLABLE        as is_nullable
,      CHARACTER_SET_NAME as character_set_name
,      COLLATION_NAME     as collation_name
,      EXTRA              as extra
from   information_schema.COLUMNS
where  TABLE_SCHEMA = %s
and    TABLE_NAME   = %s
order by ORDINAL_POSITION',
                   self::$dl->quoteString($schemaName),
                   self::$dl->quoteString($tableName));

    return self::$dl->executeRows($sql);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects all primary keys from table.
   *
   * @param string $schemaName The name of the table schema.
   * @param string $tableName  The name of the table.
   *
   * @return array[]
   */
  public static function getTablePrimaryKeys(string $schemaName, string $tableName): array
  {
    $sql = sprintf('
SHOW INDEX FROM %s.%s
WHERE Key_name = \'PRIMARY\'',
                   $schemaName,
                   $tableName);

    return self::$dl->executeRows($sql);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects all unique keys from table.
   *
   * @param string $schemaName The name of the table schema.
   * @param string $tableName  The name of the table.
   *
   * @return array[]
   */
  public static function getTableUniqueKeys(string $schemaName, string $tableName): array
  {
    $sql = sprintf('
SHOW INDEX FROM %s.%s
WHERE Non_unique = 0',
                   $schemaName,
                   $tableName);

    return self::$dl->executeRows($sql);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Selects all table names in a schema.
   *
   * @param string $schemaName The name of the schema.
   *
   * @return array[]
   */
  public static function getTablesNames(string $schemaName): array
  {
    $sql = sprintf("
select TABLE_NAME as table_name
from   information_schema.TABLES
where  TABLE_SCHEMA = %s
and    TABLE_TYPE   = 'BASE TABLE'
order by TABLE_NAME", self::$dl->quoteString($schemaName));

    return self::$dl->executeRows($sql);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Loads a stored routine.
   *
   * @param string $routineSource The source of the routine.
   */
  public static function loadRoutine(string $routineSource): void
  {
    self::executeNone($routineSource);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Escapes special characters in a string such that it can be safely used in SQL statements.
   *
   * Wrapper around [mysqli::real_escape_string](http://php.net/manual/mysqli.real-escape-string.php).
   *
   * @param string $string The string.
   *
   * @return string
   */
  public static function realEscapeString(string $string): string
  {
    return self::$dl->realEscapeString($string);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the default character set and collate.
   *
   * @param string $characterSet The character set.
   * @param string $collate      The collate.
   */
  public static function setCharacterSet(string $characterSet, string $collate): void
  {
    $sql = sprintf('set names %s collate %s', self::$dl->quoteString($characterSet), self::$dl->quoteString($collate));

    self::executeNone($sql);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the Output decorator.
   *
   * @param StratumStyle $io The Output decorator.
   */
  public static function setIo(StratumStyle $io): void
  {
    self::$io = $io;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sets the SQL mode.
   *
   * @param string $sqlMode The SQL mode.
   */
  public static function setSqlMode(string $sqlMode): void
  {
    $sql = sprintf('set sql_mode = %s', self::$dl->quoteString($sqlMode));

    self::executeNone($sql);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Logs the query on the console.
   *
   * @param string $query The query.
   */
  private static function logQuery(string $query): void
  {
    $query = trim($query);

    if (strpos($query, "\n")!==false)
    {
      // Query is a multi line query.
      self::$io->logVeryVerbose('Executing query:');
      self::$io->logVeryVerbose('<sql>%s</sql>', $query);
    }
    else
    {
      // Query is a single line query.
      self::$io->logVeryVerbose('Executing query: <sql>%s</sql>', $query);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
