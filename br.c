#include <my_global.h>
#include <mysql.h>

static void test_error(MYSQL *mysql, int status)
{
	if (status)
	{
		fprintf(stderr, "Error: %s (errno: %d)\n",
						mysql_error(mysql), mysql_errno(mysql));
		exit(1);
	}
}

static void test_stmt_error(MYSQL_STMT *stmt, int status)
{
	if (status)
	{
		fprintf(stderr, "Error: %s (errno: %d)\n",
						mysql_stmt_error(stmt), mysql_stmt_errno(stmt));
		exit(1);
	}
}

static void foo(char* sql_statement)
{
	MYSQL_STMT    *stmt;
	MYSQL_BIND    ps_params[1];  /* input parameter buffers */
	long long int int_data[1];   /* input/output values */
	my_bool       is_null[1];    /* output value nullability */
	int           status;

	MYSQL *mysql = mysql_init(NULL);

	if (mysql == NULL)
	{
			fprintf(stderr, "%s\n", mysql_error(mysql));
			exit(1);
	}

	if (mysql_real_connect(mysql, "localhost", "root", "root", "mysql", 0, NULL, 0) == NULL)
	{
			fprintf(stderr, "%s\n", mysql_error(mysql));
			mysql_close(mysql);
			exit(1);
	}

	/* initialize and prepare CALL statement with parameter placeholders */
	stmt = mysql_stmt_init(mysql);
	if (!stmt)
	{
		printf("Could not initialize statement\n");
		exit(1);
	}
	status = mysql_stmt_prepare(stmt, sql_statement, 16);
	test_stmt_error(stmt, status);

	/* initialize parameters: p_blob */
	memset(ps_params, 0, sizeof (ps_params));

	ps_params[0].buffer_type = MYSQL_TYPE_LONG;
	ps_params[0].buffer = (char *) &int_data[0];
	ps_params[0].length = 0;
	ps_params[0].is_null = 0;

	/* bind parameters */
	status = mysql_stmt_bind_param(stmt, ps_params);
	test_stmt_error(stmt, status);

	/* assign values to parameters and execute statement */
	int_data[0]= 10;  /* p_blob */

	status = mysql_stmt_execute(stmt);
	test_stmt_error(stmt, status);

	/* process results until there are no more */
	do {
		int i;
		int num_fields;       /* number of columns in result */
		MYSQL_FIELD *fields;  /* for result set metadata */
		MYSQL_BIND *rs_bind;  /* for output buffers */

		/* the column count is > 0 if there is a result set */
		/* 0 if the result is only the final status packet */
		num_fields = mysql_stmt_field_count(stmt);

		if (num_fields > 0)
		{
			/* there is a result set to fetch */
			printf("Number of columns in result: %d\n", (int) num_fields);

			/* what kind of result set is this? */
			printf("Data: ");
			if(mysql->server_status & SERVER_PS_OUT_PARAMS)
				printf("this result set contains OUT/INOUT parameters\n");
			else
				printf("this result set is produced by the procedure\n");

			MYSQL_RES *rs_metadata = mysql_stmt_result_metadata(stmt);
			test_stmt_error(stmt, rs_metadata == NULL);

			fields = mysql_fetch_fields(rs_metadata);

			rs_bind = (MYSQL_BIND *) malloc(sizeof (MYSQL_BIND) * num_fields);
			if (!rs_bind)
			{
				printf("Cannot allocate output buffers\n");
				exit(1);
			}
			memset(rs_bind, 0, sizeof (MYSQL_BIND) * num_fields);

			/* set up and bind result set output buffers */
			for (i = 0; i < num_fields; ++i)
			{
				rs_bind[i].buffer_type = fields[i].type;
				rs_bind[i].is_null = &is_null[i];

				switch (fields[i].type)
				{
					case MYSQL_TYPE_LONGLONG:
						rs_bind[i].buffer = (char *) &(int_data[i]);
						rs_bind[i].buffer_length = sizeof (int_data);
						break;

					default:
						fprintf(stderr, "ERROR: unexpected type: %d.\n", fields[i].type);
						exit(1);
				}
			}

			status = mysql_stmt_bind_result(stmt, rs_bind);
			test_stmt_error(stmt, status);

			/* fetch and display result set rows */
			while (1)
			{
				status = mysql_stmt_fetch(stmt);

				if (status == 1 || status == MYSQL_NO_DATA)
					break;

				for (i = 0; i < num_fields; ++i)
				{
					switch (rs_bind[i].buffer_type)
					{
						case MYSQL_TYPE_LONGLONG:
							if (*rs_bind[i].is_null)
								printf(" val[%d] = NULL;", i);
							else
								printf(" val[%d] = %ld;", i, (long) *((int *) rs_bind[i].buffer));
							break;

						default:
							printf("  unexpected type (%d)\n", rs_bind[i].buffer_type);
					}
				}
				printf("\n");
			}

			mysql_free_result(rs_metadata); /* free metadata */
			free(rs_bind);                  /* free output buffers */
		}
		else
		{
			/* no columns = final status packet */
			printf("End of procedure output\n");
		}

		/* more results? -1 = no, >0 = error, 0 = yes (keep looking) */
		status = mysql_stmt_next_result(stmt);
		if (status > 0)
			test_stmt_error(stmt, status);
	} while (status == 0);

	mysql_stmt_close(stmt);

	mysql_close(mysql);
}


int main(int argc, char **argv)
{
	foo("CALL tst_foo1(?)");
	foo("CALL tst_foo1(?)");

	foo("CALL tst_foo2(?)");
	foo("CALL tst_foo2(?)");
}