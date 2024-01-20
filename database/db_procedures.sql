use bookstore;

drop procedure if exists addCustomer;
delimiter //
create procedure addCustomer(
    in name varchar(255),
    in dob date,
    in phone varchar(10),
    in address text,
    in cardNumber varchar(16),
    in email varchar(255),
    in password varchar(255),
    in refEmail varchar(255)
)
begin
	declare counter int default 0;
    declare refID varchar(20) default null;
    select cast(substr(id,9) as unsigned) into counter from customer ORDER BY cast(substr(id,9) as unsigned) DESC LIMIT 1;
    set counter:=counter+1;
    insert into appUser values(concat('CUSTOMER',counter),name,dob,address,phone,email,password,null);
    if refEmail is not null then
		select customer.id into refID from customer join appUser on appUser.id=customer.id where appUser.email=refEmail;
    end if;
    insert into customer(id,cardNumber,referrer) values(concat('CUSTOMER',counter),cardNumber,refID);
end//
delimiter ;

drop procedure if exists addBook;
delimiter //
create procedure addBook(
	in name varchar(255),
    in edition int,
    in isbn varchar(13),
    in ageRestriction int,
    in publisher varchar(255),
    in publishDate date,
    in description text,
    in imagePath text,
    in physicalPrice double,
    in inStock int,
    in filePrice double,
    in pdfPath text
)
begin
	declare counter int default 0;
	select cast(substr(id,5) as unsigned) into counter from book ORDER BY cast(substr(id,5) as unsigned) DESC LIMIT 1;
    set counter=counter+1;
    insert into book values(concat('BOOK',counter),name,edition,isbn,ageRestriction,0,publisher,publishDate,true,concat('BOOK',counter,'/',imagePath),description);
    insert into physicalCopy values(concat('BOOK',counter),physicalPrice,inStock);
    insert into fileCopy values(concat('BOOK',counter),filePrice,concat('BOOK',counter,'/',pdfPath));
    select concat('BOOK',counter) as id;
end//
delimiter ;