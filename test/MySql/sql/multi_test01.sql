create temporary table TMP_FOO(foo1 int, foo2 int, foo3 int);

insert into TMP_FOO(foo1) values(1), (2);

select * from TMP_FOO;

delete from TMP_FOO where foo1=1;

select * from TMP_FOO;

describe TMP_FOO;
