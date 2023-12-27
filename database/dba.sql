create user 'bookstore'@'localhost' identified with mysql_native_password by 'bookstore123';
-- Or use this line if the above one does not work
-- create user 'bookstore'@'localhost' identified by 'bookstore123';

grant all privileges on bookstore.* to 'bookstore'@'localhost';

grant file on *.* to 'bookstore'@'localhost';