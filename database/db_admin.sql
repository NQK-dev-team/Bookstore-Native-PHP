use mysql;

-- select * from user;

drop user if exists 'bookstore'@'localhost';
create user 'bookstore'@'localhost' identified with caching_sha2_password by 'bookstore123';
-- Or use this line if the above one does not work
-- create user 'bookstore'@'localhost' identified by 'bookstore123';

grant insert,select,update,delete,execute on bookstore.* to 'bookstore'@'localhost';

FLUSH PRIVILEGES;