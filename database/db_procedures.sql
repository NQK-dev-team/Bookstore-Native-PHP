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
    in refEmail varchar(255),
    in gender varchar(1)
)
begin
	declare counter int default 0;
    declare refID varchar(20) default null;
    select cast(substr(id,9) as unsigned) into counter from customer ORDER BY cast(substr(id,9) as unsigned) DESC LIMIT 1;
    set counter:=counter+1;
    insert into appUser(id,name,dob,address,phone,email,password,imagePath,gender) values(concat('CUSTOMER',counter),name,dob,address,phone,email,password,null,gender);
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

drop procedure if exists addCustomerDiscount;
delimiter //
create procedure addCustomerDiscount(
	in name varchar(255),
    in discount double,
    in point double
)
begin
	declare counter int default 0;
	select cast(substr(id,11) as unsigned) into counter from customerDiscount ORDER BY cast(substr(id,11) as unsigned) DESC LIMIT 1;
    set counter=counter+1;
    insert into discount(id,name) values(concat('C_DISCOUNT',counter),name);
    insert into customerDiscount(id,discount,point) values(concat('C_DISCOUNT',counter),discount,point);
end//
delimiter ;

drop procedure if exists addReferrerDiscount;
delimiter //
create procedure addReferrerDiscount(
	in name varchar(255),
    in discount double,
    in people int
)
begin
	declare counter int default 0;
	select cast(substr(id,11) as unsigned) into counter from referrerDiscount ORDER BY cast(substr(id,11) as unsigned) DESC LIMIT 1;
    set counter=counter+1;
    insert into discount(id,name) values(concat('R_DISCOUNT',counter),name);
    insert into referrerDiscount(id,discount,numberOfPeople) values(concat('R_DISCOUNT',counter),discount,people);
end//
delimiter ;

drop procedure if exists addEventDiscount;
delimiter //
create procedure addEventDiscount(
	in name varchar(255),
    in discount double,
    in startDate date,
    in endDate date,
    in applyForAll bool
)
begin
	declare counter int default 0;
	select cast(substr(id,11) as unsigned) into counter from eventDiscount ORDER BY cast(substr(id,11) as unsigned) DESC LIMIT 1;
    set counter=counter+1;
    insert into discount(id,name) values(concat('E_DISCOUNT',counter),name);
    insert into eventDiscount(id,discount,startDate,endDate,applyForAll) values(concat('E_DISCOUNT',counter),discount,startDate,endDate,applyForAll);
    select concat('E_DISCOUNT',counter) as newID;
end//
delimiter ;