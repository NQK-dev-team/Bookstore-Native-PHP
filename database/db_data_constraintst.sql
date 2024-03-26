use bookstore;

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
        
		if new.password is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s account password is null!';
        end if;
        
        if new.address is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s address is null!';
        end if;
    elseif exists(select * from customer where customer.id=new.id) then
		if (select status from customer where customer.id=new.id) then
			if new.phone is null then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number is null!';
			end if;
            
            if new.password is null then
				SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s account password is null!';
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
    
    if (select password from appUser where appUser.id=new.id) is null then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s password is null!';
    end if;
    
    if (select address from appUser where appUser.id=new.id) is null then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin\'s address is null!';
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
    if new.status then
		if (select email from appUser where appUser.id=new.id) is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s email is null!';
        end if;
        
        if (select phone from appUser where appUser.id=new.id) is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s phone number is null!';
        end if;
        
        if (select password from appUser where appUser.id=new.id) is null then
			SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Customer\'s password is null!';
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
    if new.status and (select email from appUser where appUser.id=new.id) is null then
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This customer information has been deleted, activating the account is not allowed since it can cause potential problems!';
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