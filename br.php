<?php

function foo($query)
{
  $mysql = new \mysqli('localhost', 'root', 'root', 'mysql');

  $stmt  = $mysql->prepare($query);
  $stmt->execute();

  $stmt->bind_result($result);

  while ($stmt->fetch())
  {
    echo "$result\n";
  }

  $stmt->close();

  if ($mysql->more_results()) $mysql->next_result();

  $mysql->close();
}

foo("call tst_foo1('123')");
foo("call tst_foo1('123')");

foo("call tst_foo2('123')");

