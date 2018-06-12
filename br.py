from mysql.connector import DataError, MySQLConnection, InterfaceError
from mysql.connector.cursor import MySQLCursorBuffered, MySQLCursor

config = {
    'database':  'mysql',
    'user':      'root',
    'password':  'root',
    'host':      'localhost',
    'port':      3306,
    'charset':   'utf8',
    'collation': 'utf8_general_ci',
    'sql_mode':  'STRICT_ALL_TABLES'
}

connection = MySQLConnection(**config)


def foo(n):
    cursor = MySQLCursorBuffered(connection)
    itr = cursor.execute('call tst_foo%d(%%s)' % n, ('hello world', ), True)
    for tmp in itr:
        ret = tmp.fetchall()
        print(ret)

    cursor.close()


foo(1)
foo(1)

foo(2)

