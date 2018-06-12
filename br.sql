drop procedure if exists tst_foo1;

delimiter $$

create procedure tst_foo1(in p_blob int)
begin
  select 111 as foo;
end
$$

drop procedure if exists tst_foo2;

create procedure tst_foo2(in p_blob longblob)
begin
  declare l_done  boolean default false;
  declare l_table varchar(64);

  declare c_tables cursor
  for
  select table_name
  from   information_schema.TABLES
  ;

  declare continue handler for sqlstate '02000' set l_done = true;

  open c_tables;
  loop1: loop
    fetch c_tables
    into  l_table
    ;
    if (l_done) then
      close c_tables;
      leave loop1;
    end if;

    -- Nothing to do.
  end loop;

  select 222 as foo;
end
$$

