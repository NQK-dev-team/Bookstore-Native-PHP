use bookstore;

-- **** Business constraints ****



-- ** Begin of rating **
-- This trigger forbid any insert statement to `rating` table if the user hasn't bought the book yet
drop trigger if exists ratingInsertTrigger;
delimiter //
create trigger ratingInsertTrigger
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

-- ** Begin of comment **
-- This trigger forbid any insert statement to `comment` table if the user hasn't bought the book yet
drop trigger if exists commentInsertTrigger;
delimiter //
create trigger commentInsertTrigger
before insert on comment
for each row
begin
	if not (
    exists(select * from customerOrder join fileOrder on fileOrder.id=customerOrder.id join fileOrderContain on fileOrderContain.orderID=fileOrder.id
    where customerOrder.status=true and customerOrder.customerID=new.customerID and fileOrderContain.bookID=new.bookID) 
    or
    exists(select * from customerOrder join physicalOrder on physicalOrder.id=customerOrder.id join physicalOrderContain on physicalOrderContain.orderID=physicalOrder.id
    where customerOrder.status=true and customerOrder.customerID=new.customerID and physicalOrderContain.bookID=new.bookID)
    ) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer hasn\'t buy this book yet, commenting is not allowed!';
    end if;
end//
delimiter ;
-- ** End of comment **

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

-- This trigger also check if `destinationAddress` is null, if it is then get the customer default address, if that also null, return error
drop trigger if exists physicalOrderContainBusinessConstraintUpdateTrigger;
delimiter //
create trigger physicalOrderContainBusinessConstraintUpdateTrigger
before update on physicalOrderContain
for each row
begin
    if (select customerOrder.status from customerOrder where customerOrder.id=old.orderID) then
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not update content of order that has been purchased!';
	else
		begin
			declare address varchar(1000) default null;
			if new.destinationAddress is null then
				select appUser.address into address from appUser join customer on appUser.id=customer.id join customerOrder on customerOrder.customerID=customer.id where customerOrder.id=old.orderID;
				if address is null then
					SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer did not provide book\'s delivery destination address nor fill in the `address` field in the profile!';
				else
					set new.destinationAddress:=address;
				end if;
			end if;
        end;
    end if;
end//
delimiter ;

-- This trigger check if `destinationAddress` is null, if it is then get the customer default address, if that also null, return error
drop trigger if exists physicalOrderContainBusinessConstraintInsertTrigger;
delimiter //
create trigger physicalOrderContainBusinessConstraintInsertTrigger
before insert on physicalOrderContain
for each row
begin
	declare address varchar(1000) default null;
	if new.destinationAddress is null then
		select appUser.address into address from appUser join customer on appUser.id=customer.id join customerOrder on customerOrder.customerID=customer.id where customerOrder.id=new.orderID;
		if address is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer did not provide book\'s delivery destination address nor fill in the `address` field in the profile!';
		else
			set new.destinationAddress:=address;
		end if;
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

-- This trigger forbid update statements that set the end date to end earlier than it's supposed to but the discount coupon has already been used on purchased order(s) on the truncated dates (which are still at the range of the old end date)
-- This trigger also remove any associated rows in `eventApply` table if `applyForAll` column is set from false to true
drop trigger if exists eventDiscountBusinessConstraintUpdateTrigger;
delimiter //
create trigger eventDiscountBusinessConstraintUpdateTrigger
before update on eventDiscount
for each row
begin
	if old.endDate > new.endDate and exists(select * from customerOrder join discountApply on customerOrder.id=discountApply.orderID where discountApply.discountID=new.id and customerOrder.purchaseTime>new.endDate and customerOrder.purchaseTime<=old.endDate and customerOrder.status=true) then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Can not shorten discount event end date, there are purchased order(s) that have already used this coupon on dates that are outside of the new end date you want to set!';
    end if;
    
    if !old.applyForAll and new.applyForAll then
		delete from eventApply where eventApply.eventID=eventDiscount.id;
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

-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s phone number format is not valid!';
        end if;
        
        if new.dob is not null then
			if new.dob>curdate() then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin date of birth is not valid!';
            end if;
            
			if date_add(new.dob,interval 18 year)>curdate() then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin must be at least 18 years old or older!';
            end if;
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
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number format is not valid!';
		end if;
        
        if new.dob is not null then
			if new.dob>curdate() then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer date of birth is not valid!';
            end if;
            
			if date_add(new.dob,interval 18 year)>curdate() then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer must be at least 18 years old or older!';
            end if;
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
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s phone number format is not valid!';
        end if;
    end if;
    
    if (select dob from appUser where appUser.id=new.id) is not null then
		if (select dob from appUser where appUser.id=new.id)>curdate() then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin date of birth is not valid!';
        end if;
        
		if date_add((select dob from appUser where appUser.id=new.id),interval 18 year)>curdate() then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin must be at least 18 years old or older!';
        end if;
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
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s card number format is not valid!';
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
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number format is not valid!';
	end if;
    
    if (select dob from appUser where appUser.id=new.id) is not null then
		if (select dob from appUser where appUser.id=new.id)>curdate() then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer date of birth is not valid!';
        end if;
        
		if date_add((select dob from appUser where appUser.id=new.id),interval 18 year)>curdate() then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer must be at least 18 years old or older!';
        end if;
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
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s card number format is not valid!';
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
	if new.isbn is not null and not new.isbn REGEXP '^[0-9]{10,13}$' then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book ISBN number format is not valid!';
    end if;
    
    if new.publishDate is not null and new.publishDate>now() then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book\'s publish date must not be the future!';
    end if;
end//
delimiter ;

drop trigger if exists bookDataConstraintUpdateTrigger;
delimiter //
create trigger bookDataConstraintUpdateTrigger
before update on book
for each row
begin	
	if new.isbn is not null and not new.isbn REGEXP '^[0-9]{10,13}$' then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book ISBN number format is not valid!';
    end if;
    
    if new.publishDate is not null and new.publishDate>now() then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Book\'s publish date must not be the future!';
    end if;
end//
delimiter ;
-- ** End of book **

-- ** Begin of customerOrder **
drop trigger if exists customerOrderDataConstraintInsertTrigger;
delimiter //
create trigger customerOrderDataConstraintInsertTrigger
before insert on customerOrder
for each row
begin
	if new.purchaseTime>now() then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer order time must not be the future!';
    end if;
end//
delimiter ;

-- If problems arise check this!
drop trigger if exists customerOrderDataConstraintUpdateTrigger;
delimiter //
create trigger customerOrderDataConstraintUpdateTrigger
before update on customerOrder
for each row
follows orderBusinessConstraintUpdateTrigger
begin	
	if new.purchaseTime>now() then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer order time must not be the future!';
    end if;
end//
delimiter ;
-- ** End of customerOrder **

-- ** Begin of eventDiscount **
drop trigger if exists eventDiscountDataConstraintInsertTrigger;
delimiter //
create trigger eventDiscountDataConstraintInsertTrigger
before insert on eventDiscount
for each row
begin
	if new.endDate is not null and new.endDate<=curdate() then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Event discount coupon end date must be the future!';
    end if;
end//
delimiter ;
-- ** End of eventDiscount **