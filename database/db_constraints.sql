use bookstore;

-- **** Data constraints ****

-- ** Begin of appUser **
-- Note: if need to update both `customer` and `appUser` table, update the `customer` table first, then `appUser` table in order to make sure the triggers work as intended
drop trigger if exists appUserDataConstraintUpdateTrigger;
delimiter //
create trigger appUserDataConstraintUpdateTrigger
before update on appUser
for each row
begin
	if exists(select * from admin where admin.id=new.id) then
		if new.email is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s email is null!';
        elseif not new.email REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\\.[A-Z|a-z]{2,4}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s email format is not valid!';
        end if;
        
        if new.phone is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s phone number is null!';
        elseif not new.phone REGEXP '^[0-9]{10}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s phone number contain non-numeric character!';
        end if;
        
        if date_add(new.dob,interval 18 year)>curdate() then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin must be at least 18 years old or older!';
		end if;
    else
		if (select status from customer where customer.id=new.id) then
			if new.email is null then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s email is null!';
			end if;
        
			if new.phone is null then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number is null!';
			end if;
        end if;
        
        if new.email is not null and not new.email REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\\.[A-Z|a-z]{2,4}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s email format is not valid!';
		end if;
        
		if new.phone is not null and not new.phone REGEXP '^[0-9]{10}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number contain non-numeric character!';
		end if;
        
        if date_add(new.dob,interval 18 year)>curdate() then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer must be at least 18 years old or older!';
		end if;
    end if;
end//
delimiter ;
-- ** End of appUser **

-- ** Begin of admin **
drop trigger if exists adminDataConstraintInsertTrigger;
delimiter //
create trigger adminDataConstraintInsertTrigger
before insert on admin
for each row
begin
	if (select email from appUser where appUser.id=new.id) is null then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s email is null!';
    else
		if not (select email from appUser where appUser.id=new.id) REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\\.[A-Z|a-z]{2,4}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s email format is not valid!';
        end if;
    end if;
    
	if (select phone from appUser where appUser.id=new.id) is null then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s phone number is null!';
    else
		if not (select phone from appUser where appUser.id=new.id) REGEXP '^[0-9]{10}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s phone number contain non-numeric character!';
        end if;
    end if;
    
    if date_add((select dob from appUser where appUser.id=new.id),interval 18 year)>curdate() then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin must be at least 18 years old or older!';
    end if;
end//
delimiter ;
-- ** End of admin **

-- ** Begin of customer **
drop trigger if exists customerDataConstraintInsertTrigger;
delimiter //
create trigger customerDataConstraintInsertTrigger
before insert on customer
for each row
begin
	if new.cardNumber is not null and not new.cardNumber REGEXP '^[0-9]{8,16}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s card number contain non-numeric character!';
    end if;
    
    if new.status then
		if (select email from appUser where appUser.id=new.id) is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s email is null!';
        end if;
        
        if (select phone from appUser where appUser.id=new.id) is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number is null!';
        end if;
    end if;
    
    if (select email from appUser where appUser.id=new.id) is not null and not (select email from appUser where appUser.id=new.id) REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\\.[A-Z|a-z]{2,4}$' then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s email format is not valid!';
	end if;
        
	if (select phone from appUser where appUser.id=new.id) is not null and not (select phone from appUser where appUser.id=new.id) REGEXP '^[0-9]{10}$' then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number contain non-numeric character!';
	end if;
    
    if date_add((select dob from appUser where appUser.id=new.id),interval 18 year)>curdate() then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer must be at least 18 years old or older!';
    end if;
end//
delimiter ;

-- Note: if need to update both `customer` and `appUser` table, update the `customer` table first, then `appUser` table in order to make sure the triggers work as intended
drop trigger if exists customerDataConstraintUpdateTrigger;
delimiter //
create trigger customerDataConstraintUpdateTrigger
before update on customer
for each row
begin	
	if new.cardNumber is not null and not new.cardNumber REGEXP '^[0-9]{8,16}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s card number contain non-numeric character!';
    end if;
end//
delimiter ;
-- ** End of customer **

-- ** Begin of book **
drop trigger if exists bookDataConstraintInsertTrigger;
delimiter //
create trigger bookDataConstraintInsertTrigger
before insert on book
for each row
begin
	if not new.isbn REGEXP '^[0-9]{10,13}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book ISBN number contain non-numeric character!';
    end if;
end//
delimiter ;

drop trigger if exists bookDataConstraintUpdateTrigger;
delimiter //
create trigger bookDataConstraintUpdateTrigger
before update on book
for each row
begin	
	if not new.isbn REGEXP '^[0-9]{10,13}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book ISBN number contain non-numeric character!';
    end if;
end//
delimiter ;
-- ** End of book **

-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

-- **** Specialization/Generalization constraints ****

-- *** appUser super class ***
-- ** Begin of customer **
drop trigger if exists customerSGConstraintInsertTrigger;
delimiter //
create trigger customerSGConstraintInsertTrigger
before insert on customer
for each row
follows customerDataConstraintInsertTrigger
begin
	if exists(select * from admin where admin.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s ID found in admin table!';
    end if;
end//
delimiter ;

drop trigger if exists customerSGConstraintUpdateTrigger;
delimiter //
create trigger customerSGConstraintUpdateTrigger
before update on customer
for each row
follows customerDataConstraintUpdateTrigger
begin
	if exists(select * from admin where admin.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s ID found in admin table!';
    end if;
end//
delimiter ;
-- ** End of customer **

-- ** Begin of admin **
drop trigger if exists adminSGConstraintInsertTrigger;
delimiter //
create trigger adminSGConstraintInsertTrigger
before insert on admin
for each row
begin
	if exists(select * from customer where customer.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s ID found in customer table!';
    end if;
end//
delimiter ;

drop trigger if exists adminSGConstraintUpdateTrigger;
delimiter //
create trigger adminSGConstraintUpdateTrigger
before update on admin
for each row
begin
	if exists(select * from customer where customer.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s ID found in customer table!';
    end if;
end//
delimiter ;
-- ** End of admin **
-- *** appUser super class ***

-- *** Discount super class ***
-- ** Begin of customerDiscount **
drop trigger if exists customerDiscountSGConstraintInsertTrigger;
delimiter //
create trigger customerDiscountSGConstraintInsertTrigger
before insert on customerDiscount
for each row
begin
	if exists(select * from referrerDiscount where referrerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer discount coupon ID found in referrerDiscount table!';
    end if;
    if exists(select * from eventDiscount where eventDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer discount coupon ID found in eventDiscount table!';
    end if;
end//
delimiter ;

drop trigger if exists customerDiscountSGConstraintUpdateTrigger;
delimiter //
create trigger customerDiscountSGConstraintUpdateTrigger
before update on customerDiscount
for each row
begin
	if exists(select * from referrerDiscount where referrerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer discount coupon ID found in referrerDiscount table!';
    end if;
    if exists(select * from eventDiscount where eventDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer discount coupon ID found in eventDiscount table!';
    end if;
end//
delimiter ;
-- ** End of customerDiscount **

-- ** Begin of referrerDiscount **
drop trigger if exists referrerDiscountSGConstraintInsertTrigger;
delimiter //
create trigger referrerDiscountSGConstraintInsertTrigger
before insert on referrerDiscount
for each row
begin
	if exists(select * from customerDiscount where customerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Referrer discount coupon ID found in customerDiscount table!';
    end if;
    if exists(select * from eventDiscount where eventDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Referrer discount coupon ID found in eventDiscount table!';
    end if;
end//
delimiter ;

drop trigger if exists referrerDiscountSGConstraintUpdateTrigger;
delimiter //
create trigger referrerDiscountSGConstraintUpdateTrigger
before update on referrerDiscount
for each row
begin
	if exists(select * from customerDiscount where customerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Referrer discount coupon ID found in customerDiscount table!';
    end if;
    if exists(select * from eventDiscount where eventDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Referrer discount coupon ID found in eventDiscount table!';
    end if;
end//
delimiter ;
-- ** End of referrerDiscount **

-- ** Begin of eventDiscount **
drop trigger if exists eventDiscountSGConstraintInsertTrigger;
delimiter //
create trigger eventDiscountSGConstraintInsertTrigger
before insert on eventDiscount
for each row
begin
	if exists(select * from customerDiscount where customerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Event discount coupon ID found in customerDiscount table!';
    end if;
    if exists(select * from referrerDiscount where referrerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Event discount coupon ID found in referrerDiscount table!';
    end if;
end//
delimiter ;

drop trigger if exists eventDiscountSGConstraintUpdateTrigger;
delimiter //
create trigger eventDiscountSGConstraintUpdateTrigger
before update on eventDiscount
for each row
begin
	if exists(select * from customerDiscount where customerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Event discount coupon ID found in customerDiscount table!';
    end if;
    if exists(select * from referrerDiscount where referrerDiscount.id=new.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Event discount coupon ID found in referrerDiscount table!';
    end if;
end//
delimiter ;
-- ** End of eventDiscount **
-- *** Discount super class ***

-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

-- **** Business constraints ****

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

drop trigger if exists orderBusinessConstraintUpdateTrigger;
delimiter //
create trigger orderBusinessConstraintUpdateTrigger
before update on customerOrder
for each row
begin
    if old.status then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update order that has been purchased!';
    end if;
end//
delimiter ;
-- ** End of customerOrder **

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
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update order that has been purchased!';
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

drop trigger if exists physicalOrderBusinessConstraintUpdateTrigger;
delimiter //
create trigger physicalOrderBusinessConstraintUpdateTrigger
before update on physicalOrder
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.id) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update order that has been purchased!';
    end if;
end//
delimiter ;
-- ** End of physicalOrder **

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
-- ** End of physicalOrderContain **

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
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been apply on purchased order(s)!';
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
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been apply on purchased order(s)!';
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
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been apply on purchased order(s)!';
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
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete discount coupon that has been apply on purchased order(s)!';
    end if;
end//
delimiter ;
-- ** End of eventDiscount **

-- ** Begin of eventApply **
-- This trigger forbid any delete statement to any row of `eventApply` table that the discount event coupon is used on purchased order(s) and the book that discount event applies is also in that/those purchased order(s)
drop trigger if exists eventApplyBusinessConstraintDeleteTrigger;
delimiter //
create trigger eventApplyBusinessConstraintDeleteTrigger
before delete on eventApply
for each row
begin
	if not (select eventDiscount.applyForAll from eventDiscount where eventDiscount.id=old.eventID) and exists(
		select customerOrder.id from customerOrder 
        join discountApply on discountApply.orderID=customerOrder.id
        where customerOrder.status=true and discountApply.discountID=old.eventID and customerOrder.id in (
			select customerOrder.id from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=old.bookID
            union
			select customerOrder.id from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=old.bookID
        )
    )
    then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not delete this discount_event-book row, the discount event has been used on purchased order(s) and the book is also in that/those order(s)!';
    end if;
end//
delimiter ;
-- ** End of eventApply **