use bookstore;

drop event if exists deleteCustomerEvent;
DELIMITER //
CREATE EVENT deleteCustomerEvent
ON SCHEDULE EVERY 5 minute
DO
  BEGIN
	  -- SET SQL_SAFE_UPDATES = 0;
      update appUser join customer on appUser.id = customer.id set email=null,phone=null,deleteTime=null,cardNumber=null,referrer=null,imagePath=null,address=null,password=null,name='ACCOUNT REMOVED' where status=0 and deleteTime is not null and deleteTime<=now();
      -- SET SQL_SAFE_UPDATES = 1;
  END//
DELIMITER ;