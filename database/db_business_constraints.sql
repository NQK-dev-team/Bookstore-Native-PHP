use bookstore;

-- **** Business constraints ****

-- ** Begin of fileOrderContain **
-- These 2 triggers below forbid any delete or update statement to any row of `fileOrderContain` table that has `status` set to true (order has been purchased)
drop trigger if exists fileOrderContainBusinessConstraintDeleteTrigger;
delimiter //
create trigger fileOrderContainBusinessConstraintDeleteTrigger
before delete on fileOrderContain
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.orderID) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete content of order that has been purchased!';
    end if;
end//
delimiter ;

drop trigger if exists fileOrderContainBusinessConstraintUpdateTrigger;
delimiter //
create trigger fileOrderContainBusinessConstraintUpdateTrigger
before update on fileOrderContain
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.orderID) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update content of order that has been purchased!';
    end if;
end//
delimiter ;

drop trigger if exists fileOrderContainBusinessConstraintInsertTrigger;
delimiter //
create trigger fileOrderContainBusinessConstraintInsertTrigger
before insert on fileOrderContain
for each row
begin
    declare customerID varchar(20);
    
    select customerOrder.customerID into customerID from customerOrder where id=new.orderID;
    
    if exists(select * from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID and customerOrder.status=true and customerOrder.customerID=customerID where fileOrderContain.orderID!=new.orderID and fileOrderContain.bookID=new.bookID) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This customer has already bought this book!';
    end if;
    
    if (select price from fileCopy where id=new.bookID) is null or (select filePath from fileCopy where id=new.bookID) is null then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This e-book is not available for purchase!';
    end if;
end//
delimiter ;

-- This trigger will delete the empty order
-- drop trigger if exists fileOrderContainBusinessConstraintDeleteTrigger2;
-- delimiter //
-- create trigger fileOrderContainBusinessConstraintDeleteTrigger2
-- after delete on fileOrderContain
-- for each row
-- begin                   
-- 		delete from fileOrder where id not in(
-- 			select orderID from fileOrderContain
-- 		);
--             
-- 		delete from customerOrder where id not in(
-- 			select id from fileOrder
-- 			union
-- 			select id from physicalOrder
-- 		);
-- end//
-- delimiter ;
-- ** End of fileOrderContain **

-- ** Begin of physicalOrderContain **
-- These 2 triggers below forbid any delete or update statement to any row of `physicalOrderContain` table that has `status` set to true (order has been purchased)
drop trigger if exists physicalOrderContainBusinessConstraintDeleteTrigger;
delimiter //
create trigger physicalOrderContainBusinessConstraintDeleteTrigger
before delete on physicalOrderContain
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.orderID) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete content of order that has been purchased!';
    end if;
end//
delimiter ;

drop trigger if exists physicalOrderContainBusinessConstraintUpdateTrigger;
delimiter //
create trigger physicalOrderContainBusinessConstraintUpdateTrigger
before update on physicalOrderContain
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.orderID) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update content of order that has been purchased!';
    end if;
end//
delimiter ;

drop trigger if exists physicalOrderContainBusinessConstraintInsertTrigger;
delimiter //
create trigger physicalOrderContainBusinessConstraintInsertTrigger
before insert on physicalOrderContain
for each row
begin
    if (select price from physicalCopy where id=new.bookID) is null or (select inStock from physicalCopy where id=new.bookID)=0 then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This hardcover is not available for purchase!';
    end if;
end//
delimiter ;

-- This trigger will delete the empty order
-- drop trigger if exists physicalOrderContainBusinessConstraintDeleteTrigger2;
-- delimiter //
-- create trigger physicalOrderContainBusinessConstraintDeleteTrigger2
-- after delete on physicalOrderContain
-- for each row
-- begin                   
-- 		delete from physicalOrder where id not in(
-- 			select orderID from physicalOrderContain
-- 		);
--             
-- 		delete from customerOrder where id not in(
-- 			select id from fileOrder
-- 			union
-- 			select id from physicalOrder
-- 		);
-- end//
-- delimiter ;
-- ** End of physicalOrderContain **

-- ** Begin of book **
-- This trigger delete the book from unpaid orders if new.status=false
drop trigger if exists bookBusinessConstraintUpdateTrigger;
delimiter //
create trigger bookBusinessConstraintUpdateTrigger
before update on book
for each row
begin	
	if not new.status then
		DELETE fileOrderContain FROM fileOrderContain JOIN customerOrder ON fileOrderContain.orderID = customerOrder.id WHERE customerOrder.status = false AND fileOrderContain.bookID = new.id;
		
		DELETE physicalOrderContain FROM physicalOrderContain JOIN customerOrder ON physicalOrderContain.orderID = customerOrder.id WHERE customerOrder.status = false AND physicalOrderContain.bookID = new.id;
                        
		delete fileOrder from fileOrder join customerOrder on customerOrder.id=fileOrder.id where customerOrder.id not in(
			select orderID from fileOrderContain
		) and status=false;
            
		delete physicalOrder from physicalOrder join customerOrder on customerOrder.id=physicalOrder.id where customerOrder.id not in(
			select orderID from physicalOrderContain
		) and status=false;
            
		delete from customerOrder where id not in(
			select id from fileOrder
			union
			select id from physicalOrder
		) and status=false;
            
		-- begin
-- 			DECLARE done BOOLEAN DEFAULT FALSE;
-- 			declare orderID varchar(20) default null;
-- 			DECLARE myCursor CURSOR FOR SELECT id from customerOrder where status=false;
-- 			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
-- 			OPEN myCursor;
-- 				loop_start: LOOP
-- 					set orderID:=null;
-- 					FETCH myCursor INTO orderID;
-- 					IF done THEN
-- 						LEAVE loop_start;
-- 					END IF;
-- 					call reEvaluateOrder(orderID);
-- 					END LOOP loop_start;
-- 			CLOSE myCursor;
-- 		end;
    end if;
end//
delimiter ;
-- ** End of book **

-- ** Begin of rating **
-- This trigger forbid any insert statement to `rating` table if the user hasn't bought the book yet
drop trigger if exists ratingInsertTrigger1;
delimiter //
create trigger ratingInsertTrigger1
before insert on rating
for each row
begin
	if not (
    exists(select * from customerOrder join fileOrder on fileOrder.id=customerOrder.id join fileOrderContain on fileOrderContain.orderID=fileOrder.id
    where customerOrder.status=true and customerOrder.customerID=new.customerID and fileOrderContain.bookID=new.bookID) 
    or
    exists(select * from customerOrder join physicalOrder on physicalOrder.id=customerOrder.id join physicalOrderContain on physicalOrderContain.orderID=physicalOrder.id
    where customerOrder.status=true and customerOrder.customerID=new.customerID and physicalOrderContain.bookID=new.bookID)
    ) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer hasn\'t buy this book yet, rating is not allowed!';
    end if;
end//
delimiter ;
-- ** End of rating **

-- ** Begin of customerOrder **
-- These 2 triggers below forbid any delete or update statement to any row of `customerOrder` table that has `status` set to true (order has been purchased)
drop trigger if exists orderBusinessConstraintDeleteTrigger;
delimiter //
create trigger orderBusinessConstraintDeleteTrigger
before delete on customerOrder
for each row
begin
    if old.status then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete order that has been purchased!';
    end if;
end//
delimiter ;

drop trigger if exists orderBusinessConstraintInsertTrigger;
delimiter //
create trigger orderBusinessConstraintInsertTrigger
before insert on customerOrder
for each row
begin
    if not new.status and exists(select * from customerOrder where customerID=new.customerID and status=false) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This customer has already had an unpaid order, cannot insert another!';
    end if;
end//
delimiter ;

drop trigger if exists orderBusinessConstraintUpdateTrigger;
delimiter //
create trigger orderBusinessConstraintUpdateTrigger
before update on customerOrder
for each row
begin
    if old.status then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update information of order that has been purchased!';
    end if;
end//
delimiter ;
-- ** End of customerOrder **

-- ** Begin of discountApply **
-- These 2 triggers below forbid any delete or update statement to any row of `discountApply` table that has order `status` set to true (order has been purchased)
drop trigger if exists discountApplyBusinessConstraintDeleteTrigger;
delimiter //
create trigger discountApplyBusinessConstraintDeleteTrigger
before delete on discountApply
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.orderID) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount applied for purchased order!';
    end if;
end//
delimiter ;

drop trigger if exists discountApplyBusinessConstraintUpdateTrigger;
delimiter //
create trigger discountApplyBusinessConstraintUpdateTrigger
before update on discountApply
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.orderID) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update information of discount applied for purchased order!';
    end if;
end//
delimiter ;
-- ** End of discountApply **

-- ** Begin of fileOrder **
-- These 2 triggers below forbid any delete or update statement to any row of `fileOrder` table that has `status` set to true (order has been purchased)
drop trigger if exists fileOrderBusinessConstraintDeleteTrigger;
delimiter //
create trigger fileOrderBusinessConstraintDeleteTrigger
before delete on fileOrder
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete order that has been purchased!';
    end if;
end//
delimiter ;

drop trigger if exists fileOrderBusinessConstraintUpdateTrigger;
delimiter //
create trigger fileOrderBusinessConstraintUpdateTrigger
before update on fileOrder
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update information of order that has been purchased!';
    end if;
end//
delimiter ;
-- ** End of fileOrder **

-- ** Begin of physicalOrder **
-- These 2 triggers below forbid any delete or update statement to any row of `physicalOrder` table that has `status` set to true (order has been purchased)
drop trigger if exists physicalOrderBusinessConstraintDeleteTrigger;
delimiter //
create trigger physicalOrderBusinessConstraintDeleteTrigger
before delete on physicalOrder
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete order that has been purchased!';
    end if;
end//
delimiter ;

-- This trigger also check if `destinationAddress` is null, if it is then get the customer default address, if that also null, return error
drop trigger if exists physicalOrderBusinessConstraintUpdateTrigger;
delimiter //
create trigger physicalOrderBusinessConstraintUpdateTrigger
before update on physicalOrder
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update information of order that has been purchased!';
    end if;
    begin
	declare address varchar(1000) default null;
	if new.destinationAddress is null then
		select appUser.address into address from appUser join customer on appUser.id=customer.id join customerOrder on customerOrder.customerID=customer.id where customerOrder.id=old.id;
		if address is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer did not provide book\'s delivery destination address nor fill in the `address` field in the profile!';
		else
			set new.destinationAddress:=address;
		end if;
	end if;
    end;
end//
delimiter ;

-- This trigger check if `destinationAddress` is null, if it is then get the customer default address, if that also null, return error
drop trigger if exists physicalOrderBusinessConstraintInsertTrigger;
delimiter //
create trigger physicalOrderBusinessConstraintInsertTrigger
before insert on physicalOrder
for each row
begin
	declare address varchar(1000) default null;
	if new.destinationAddress is null then
		select appUser.address into address from appUser join customer on appUser.id=customer.id join customerOrder on customerOrder.customerID=customer.id where customerOrder.id=new.id;
		if address is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer did not provide book\'s delivery destination address nor fill in the `address` field in the profile!';
		else
			set new.destinationAddress:=address;
		end if;
	end if;
end//
delimiter ;
-- ** End of physicalOrder **

-- ** Begin of appUser **
-- This trigger forbid any delete statement to any row of `appUser` table that is a customer and has purchase an order
drop trigger if exists appUserBusinessConstraintDeleteTrigger;
delimiter //
create trigger appUserBusinessConstraintDeleteTrigger
before delete on appUser
for each row
begin
    if exists(select * from customer where customer.id=old.id) and exists(select * from customerOrder where customerID=old.id and customerOrder.status=true) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete customer that has purchased order(s)!';
    end if;
end//
delimiter ;
-- ** End of appUser **

-- ** Begin of customer **
-- This trigger forbid any delete statement to any row of `customer` table that has purchase an order
drop trigger if exists customerBusinessConstraintDeleteTrigger;
delimiter //
create trigger customerBusinessConstraintDeleteTrigger
before delete on customer
for each row
begin
    if exists(select * from customerOrder where customerID=old.id and customerOrder.status=true) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete customer that has purchased order(s)!';
    end if;
end//
delimiter ;
-- ** End of customer **

-- ** Begin of book **
-- This trigger forbid any delete statement to any row of `book` table that has been purchased
drop trigger if exists bookBusinessConstraintDeleteTrigger;
delimiter //
create trigger bookBusinessConstraintDeleteTrigger
before delete on book
for each row
begin
	if exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=old.id) 
    or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=old.id) 
    then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete book that has been purchased!';
    end if;
end//
delimiter ;
-- ** End of book **

-- ** Begin of fileCopy **
-- This trigger forbid any delete statement to any row of `fileCopy` table that has been purchased
drop trigger if exists fileCopyBusinessConstraintDeleteTrigger;
delimiter //
create trigger fileCopyBusinessConstraintDeleteTrigger
before delete on fileCopy
for each row
begin
	if exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=old.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete book that has been purchased!';
    end if;
end//
delimiter ;

-- This trigger prevent remove filePath if a customer has bought the file copy
-- If filePath is null or price is null and there are no paid order for this file copy, remove it from all unpaid orders and re-evaluate them
drop trigger if exists fileCopyBusinessConstraintUpdateTrigger;
delimiter //
create trigger fileCopyBusinessConstraintUpdateTrigger
before update on fileCopy
for each row
begin
	if new.filePath is null and exists(select * from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where fileOrderContain.bookID=new.id and customerOrder.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This file copy of the book is in an order that has been paid for, can not remove it PDF file!';
    end if;
    
    if new.filePath is null or new.price is null then
		DELETE fileOrderContain FROM fileOrderContain JOIN customerOrder ON fileOrderContain.orderID = customerOrder.id WHERE customerOrder.status = false AND fileOrderContain.bookID = new.id;
                                    
		delete fileOrder from fileOrder join customerOrder on customerOrder.id=fileOrder.id where customerOrder.id not in(
			select orderID from fileOrderContain
		) and status=false;
            
		delete from customerOrder where id not in(
			select id from fileOrder
			union
			select id from physicalOrder
		) and status=false;
            
		-- begin
-- 			DECLARE done BOOLEAN DEFAULT FALSE;
-- 			declare orderID varchar(20) default null;
-- 			DECLARE myCursor CURSOR FOR SELECT id from customerOrder where status=false;
-- 			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
-- 			OPEN myCursor;
-- 				loop_start: LOOP
-- 					set orderID:=null;
-- 					FETCH myCursor INTO orderID;
-- 					IF done THEN
-- 						LEAVE loop_start;
-- 					END IF;
-- 					call reEvaluateOrder(orderID);
-- 					END LOOP loop_start;
-- 			CLOSE myCursor;
-- 		end;
    end if;
end//
delimiter ;
-- ** End of fileCopy **

-- ** Begin of physicalCopy **
-- This trigger forbid any delete statement to any row of `physicalCopy` table that has been purchased
drop trigger if exists physicalCopyBusinessConstraintDeleteTrigger;
delimiter //
create trigger physicalCopyBusinessConstraintDeleteTrigger
before delete on physicalCopy
for each row
begin
	if exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=old.id) 
    then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete book that has been purchased!';
    end if;
end//
delimiter ;

-- If inStock is null or price is null and there are no paid order for this physical copy, remove it from all unpaid orders and re-evaluate them
drop trigger if exists physicalCopyBusinessConstraintUpdateTrigger;
delimiter //
create trigger physicalCopyBusinessConstraintUpdateTrigger
before update on physicalCopy
for each row
begin
	if new.inStock is null or new.price is null then            
		DELETE physicalOrderContain FROM physicalOrderContain JOIN customerOrder ON physicalOrderContain.orderID = customerOrder.id WHERE customerOrder.status = false AND physicalOrderContain.bookID = new.id;
            
		delete physicalOrder from physicalOrder join customerOrder on customerOrder.id=physicalOrder.id where customerOrder.id not in(
			select orderID from physicalOrderContain
		) and status=false;
            
		delete from customerOrder where id not in(
			select id from fileOrder
			union
			select id from physicalOrder
		) and status=false;
            
		-- begin
-- 			DECLARE done BOOLEAN DEFAULT FALSE;
-- 			declare orderID varchar(20) default null;
-- 			DECLARE myCursor CURSOR FOR SELECT id from customerOrder where status=false;
-- 			DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
-- 			OPEN myCursor;
-- 				loop_start: LOOP
-- 					set orderID:=null;
-- 					FETCH myCursor INTO orderID;
-- 					IF done THEN
-- 						LEAVE loop_start;
-- 					END IF;
-- 					call reEvaluateOrder(orderID);
-- 					END LOOP loop_start;
-- 			CLOSE myCursor;
-- 		end;
    end if;
end//
delimiter ;
-- ** End of physicalCopy **

-- ** Begin of discount **
-- This trigger forbid any delete statement to any row of `discount` table that has been apply on purchased order(s)
drop trigger if exists discountBusinessConstraintDeleteTrigger;
delimiter //
create trigger discountBusinessConstraintDeleteTrigger
before delete on discount
for each row
begin
	if exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=old.id) 
    then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been applied for purchased order(s)!';
    end if;
end//
delimiter ;

drop trigger if exists discountBusinessConstraintInsertTrigger;
delimiter //
create trigger discountBusinessConstraintInsertTrigger
before insert on discount
for each row
begin
	if new.status then
    if exists(select * from discount where discount.name=new.name and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Current coupon name has already been used in another coupon!';
    end if;
    end if;
end//
delimiter ;

drop trigger if exists discountBusinessConstraintUpdateTrigger;
delimiter //
create trigger discountBusinessConstraintUpdateTrigger
before update on discount
for each row
begin
	if new.status then
    if exists(select * from discount where discount.id!=new.id and discount.name=new.name and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Current coupon name has already been used in another coupon!';
    end if;
    
	-- if exists(select * from customerDiscount where customerDiscount.id=new.id) then
-- 		begin
-- 			declare pointMileStone double default null;
--             declare discountPer double default null;
--             
--             select discount,point into discountPer,pointMileStone from customerDiscount where customerDiscount.id=new.id;
--             
--             if exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.discount-discountPer)<10e-9 and customerDiscount.id!=new.id and discount.status=true) then
-- 				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not activate this coupon, current discount percentage has already been used in another coupon!';
-- 			end if;
--             
--             if exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.point-pointMileStone)<10e-9 and customerDiscount.id!=new.id and discount.status=true) then
-- 				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not activate this coupon, current accumulated point milestone has already been used in another coupon!';
-- 			end if;
--         end;
--     end if;
--     
--     if exists(select * from referrerDiscount where referrerDiscount.id=new.id) then
-- 		begin
-- 			declare peopleMileStone int default null;
--             declare discountPer double default null;
--             
--             select discount,numberOfPeople into discountPer,peopleMileStone from referrerDiscount where referrerDiscount.id=new.id;
--             
--             if exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where abs(referrerDiscount.discount-discountPer)<10e-9 and referrerDiscount.id!=new.id and discount.status=true) then
-- 				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not activate this coupon, current discount percentage has already been used in another coupon!';
-- 			end if;
--             
--             if exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.numberOfPeople=peopleMileStone and referrerDiscount.id!=new.id and discount.status=true) then
-- 				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not activate this coupon, current number of people milestone has already been used in another coupon!';
-- 			end if;
--         end;
--     end if;
    end if;
end//
delimiter ;
-- ** End of discount **

-- ** Begin of referrerDiscount **
-- This trigger forbid any delete statement to any row of `referrerDiscount` table that has been apply on purchased order(s)
drop trigger if exists referrerDiscountBusinessConstraintDeleteTrigger;
delimiter //
create trigger referrerDiscountBusinessConstraintDeleteTrigger
before delete on referrerDiscount
for each row
begin
	if exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=old.id) 
    then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been applied for purchased order(s)!';
    end if;
end//
delimiter ;

drop trigger if exists referrerDiscountBusinessConstraintInsertTrigger;
delimiter //
create trigger referrerDiscountBusinessConstraintInsertTrigger
before insert on referrerDiscount
for each row
begin
	if (select status from discount where discount.id=new.id) and exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where abs(referrerDiscount.discount-new.discount)<10e-9 and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not create this coupon, current discount percentage has already been used in another coupon!';
    end if;

	if (select status from discount where discount.id=new.id) and exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.numberOfPeople=new.numberOfPeople and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not create this coupon, current number of people milestone has already been used in another coupon!';
    end if;
end//
delimiter ;

drop trigger if exists referrerDiscountBusinessConstraintUpdateTrigger;
delimiter //
create trigger referrerDiscountBusinessConstraintUpdateTrigger
before update on referrerDiscount
for each row
begin
	if (select status from discount where discount.id=new.id) and exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where abs(referrerDiscount.discount-new.discount)<10e-9 and referrerDiscount.id!=new.id and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update this coupon, current discount percentage has already been used in another coupon!';
    end if;
    
	if (select status from discount where discount.id=new.id) and exists(select * from referrerDiscount join discount on discount.id=referrerDiscount.id where referrerDiscount.numberOfPeople=new.numberOfPeople and referrerDiscount.id!=new.id and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update this coupon, current number of people milestone has already been used in another coupon!';
    end if;
end//
delimiter ;
-- ** End of referrerDiscount **

-- ** Begin of customerDiscount **
-- This trigger forbid any delete statement to any row of `customerDiscount` table that has been apply on purchased order(s)
drop trigger if exists customerDiscountBusinessConstraintDeleteTrigger;
delimiter //
create trigger customerDiscountBusinessConstraintDeleteTrigger
before delete on customerDiscount
for each row
begin
	if exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=old.id) 
    then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been applied for purchased order(s)!';
    end if;
end//
delimiter ;

drop trigger if exists customerDiscountBusinessConstraintInsertTrigger;
delimiter //
create trigger customerDiscountBusinessConstraintInsertTrigger
before insert on customerDiscount
for each row
begin
	if (select status from discount where discount.id=new.id) and exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.discount-new.discount)<10e-9 and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not create this coupon, current discount percentage has already been used in another coupon!';
    end if;

	if (select status from discount where discount.id=new.id) and exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.point-new.point)<10e-9 and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not create this coupon, current accumulated point milestone has already been used in another coupon!';
    end if;
end//
delimiter ;

drop trigger if exists customerDiscountBusinessConstraintUpdateTrigger;
delimiter //
create trigger customerDiscountBusinessConstraintUpdateTrigger
before update on customerDiscount
for each row
begin
	if (select status from discount where discount.id=new.id) and exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.discount-new.discount)<10e-9 and customerDiscount.id!=new.id and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update this coupon, current discount percentage has already been used in another coupon!';
    end if;

	if (select status from discount where discount.id=new.id) and exists(select * from customerDiscount join discount on discount.id=customerDiscount.id where abs(customerDiscount.point-new.point)<10e-9 and customerDiscount.id!=new.id and discount.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update this coupon, current accumulated point milestone has already been used in another coupon!';
    end if;
end//
delimiter ;
-- ** End of customerDiscount **

-- ** Begin of eventDiscount **
-- This trigger forbid any delete statement to any row of `eventDiscount` table that has been apply on purchased order(s)
drop trigger if exists eventDiscountBusinessConstraintDeleteTrigger;
delimiter //
create trigger eventDiscountBusinessConstraintDeleteTrigger
before delete on eventDiscount
for each row
begin
	if exists(select * from discountApply join customerOrder on discountApply.orderID=customerOrder.id where customerOrder.status=true and discountApply.discountID=old.id) 
    then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been applied for purchased order(s)!';
    end if;
end//
delimiter ;

-- This trigger forbid update statements that push the start date up but the discount coupon has already been used on purchased order(s) on the truncated dates (which are still at the range of the start end date)
-- This trigger also forbid update statements that set the end date to end earlier than it's supposed to but the discount coupon has already been used on purchased order(s) on the truncated dates (which are still at the range of the old end date)
drop trigger if exists eventDiscountBusinessConstraintBeforeUpdateTrigger;
delimiter //
create trigger eventDiscountBusinessConstraintBeforeUpdateTrigger
before update on eventDiscount
for each row
begin
	if old.startDate < new.startDate and exists(select * from customerOrder join discountApply on customerOrder.id=discountApply.orderID where discountApply.discountID=new.id and customerOrder.purchaseTime<new.startDate and customerOrder.purchaseTime>=old.startDate and customerOrder.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot push up event start date due to existing purchased order(s) using this coupon on dates before the new start date!';
    end if;
    
	if old.endDate > new.endDate and exists(select * from customerOrder join discountApply on customerOrder.id=discountApply.orderID where discountApply.discountID=new.id and customerOrder.purchaseTime>new.endDate and customerOrder.purchaseTime<=old.endDate and customerOrder.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot shorten event end date due to existing purchased order(s) using this coupon on dates after the new end date!';
    end if;
end//
delimiter ;

-- This trigger remove any associated rows in `eventApply` table if `applyForAll` column is set from false to true
drop trigger if exists eventDiscountBusinessConstraintAfterUpdateTrigger;
delimiter //
create trigger eventDiscountBusinessConstraintAfterUpdateTrigger
after update on eventDiscount
for each row
begin
    if new.applyForAll then
		delete from eventApply where eventApply.eventID=new.id;
    end if;
end//
delimiter ;

-- ** End of eventDiscount **