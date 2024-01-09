use bookstore;

drop procedure if exists addCustomer;
delimiter //
create procedure addCustomer(
    in name varchar(100),
    in dob date,
    in phone varchar(10),
    in address text,
    in cardNumber varchar(16),
    in email varchar(100),
    in password varchar(255),
    in refEmail varchar(100)
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