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

drop procedure if exists reEvaluateOrder;
delimiter //
create procedure reEvaluateOrder(
	in orderID varchar(20)
)
begin
	declare localTotalCost double default 0;
    declare localTotalDiscount double default 0;

	update customerOrder set totalCost=0,totalDiscount=0 where id=orderID;
    delete from discountApply where discountApply.orderID=orderID;
    
    begin 
		DECLARE done BOOLEAN DEFAULT FALSE;
		declare bookID varchar(20) default null;
        declare bookAmount int default null;
		DECLARE myCursor CURSOR FOR SELECT physicalOrderContain.bookID,amount from physicalOrderContain where physicalOrderContain.orderID=orderID;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
		OPEN myCursor;
			loop_start: LOOP
				set bookID:=null;
                set bookAmount:=null;
				FETCH myCursor INTO bookID,bookAmount;
				IF done THEN
					LEAVE loop_start;
				END IF;
                begin
					declare discountID varchar(20) default null;
                    declare discount double default null;
                    select distinct combined.id,combined.discount into discountID,discount from (
						select distinct id,eventDiscount.discount from eventDiscount where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct id,eventDiscount.discount from eventDiscount join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate() and eventApply.bookID=bookID
                    ) as combined order by combined.discount desc limit 1;
                    
                    if discountID is not null then
						 set localTotalCost:=localTotalCost+(select price from physicalCopy where physicalCopy.id=bookID)*bookAmount*(100-discount)/100.0;
						 set localTotalDiscount:=localTotalDiscount+(select price from physicalCopy where physicalCopy.id=bookID)*bookAmount*discount/100.0;
                         
                         if not exists(select * from discountApply where discountApply.orderID=orderID and discountApply.discountID=discountID) then
							insert into discountApply values(orderID,discountID);
                         end if;
					end if;
                end;
				END LOOP loop_start;
		CLOSE myCursor;
    end;
    
    begin 
		DECLARE done BOOLEAN DEFAULT FALSE;
		declare bookID varchar(20) default null;
		DECLARE myCursor CURSOR FOR SELECT fileOrderContain.bookID from fileOrderContain where fileOrderContain.orderID=orderID;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
		OPEN myCursor;
			loop_start: LOOP
				set bookID:=null;
				FETCH myCursor INTO bookID;
				IF done THEN
					LEAVE loop_start;
				END IF;
                begin
					declare discountID varchar(20) default null;
                    declare discount double default null;
                    select distinct combined.id,combined.discount into discountID,discount from (
						select distinct id,eventDiscount.discount from eventDiscount where eventDiscount.applyForAll=true and eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate()
                        union
                        select distinct id,eventDiscount.discount from eventDiscount join eventApply on eventDiscount.applyForAll=false and eventDiscount.id=eventApply.eventID where eventDiscount.startDate<=curdate() and eventDiscount.endDate>=curdate() and eventApply.bookID=bookID
                    ) as combined order by combined.discount desc limit 1;
                    
                    if discountID is not null then
						 set localTotalCost:=localTotalCost+(select price from fileCopy where fileCopy.id=bookID)*(100-discount)/100.0;
						 set localTotalDiscount:=localTotalDiscount+(select price from fileCopy where fileCopy.id=bookID)*discount/100.0;
                         
                         if not exists(select * from discountApply where discountApply.orderID=orderID and discountApply.discountID=discountID) then
							insert into discountApply values(orderID,discountID);
                         end if;
					end if;
                end;
				END LOOP loop_start;
		CLOSE myCursor;
    end;
    
    begin
		declare discountID varchar(20) default null;
        declare discount double default null;
        
        select id,customerDiscount.discount into discountID,discount from customerDiscount where customerDiscount.point<=(select point from customer where id=(select customerID from customerOrder where customerOrder.id=orderID)) order by customerDiscount.discount desc limit 1;
        
        if discountID is not null then
			insert into discountApply values(orderID,discountID);
            set localTotalDiscount:=localTotalDiscount+localTotalCost*discount/100.0;
            set localTotalCost:=localTotalCost*(100-discount)/100.0;
        end if;
    end;
    
    begin
		declare discountID varchar(20) default null;
        declare discount double default null;
        
        select id,referrerDiscount.discount into discountID,discount from referrerDiscount where referrerDiscount.numberOfPeople<=(select count(*) from customer where referrer=(select customerID from customerOrder where customerOrder.id=orderID)) order by referrerDiscount.discount desc limit 1;
        
        if discountID is not null then
			insert into discountApply values(orderID,discountID);
            set localTotalDiscount:=localTotalDiscount+localTotalCost*discount/100.0;
            set localTotalCost:=localTotalCost*(100-discount)/100.0;
        end if;
    end;
    
    update customerOrder set totalCost=localTotalCost,totalDiscount=localTotalDiscount where id=orderID; 
end//
delimiter ;

-- call reEvaluateOrder('ORDER3');
-- select * from customerOrder where id='ORDER3';
-- select * from discountApply where orderID='ORDER3';