drop user if exists 'test'@'localhost';

create user 'test'@'localhost' identified by 'test';

grant all on test.*  to 'test'@'localhost';
