use bookstore;

SET GLOBAL event_scheduler = on;
-- SHOW VARIABLES LIKE 'event_scheduler';

drop table if exists orderReEvaluatedLog;
CREATE TABLE IF NOT EXISTS orderReEvaluatedLog (
    logId INT AUTO_INCREMENT PRIMARY KEY,
    executionTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    orderReEvaluated INT
);

-- select * from orderReEvaluatedLog; 

drop event if exists orderReEvaluate;
DELIMITER //
CREATE EVENT IF NOT EXISTS orderReEvaluate
ON SCHEDULE EVERY 2 minute
DO
BEGIN
	declare counter int default 0;
    begin
		DECLARE done BOOLEAN DEFAULT FALSE;
		declare orderID varchar(20) default null;
		DECLARE myCursor CURSOR FOR SELECT id from customerOrder where status=false;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
		OPEN myCursor;
			loop_start: LOOP
				set orderID:=null;
				FETCH myCursor INTO orderID;
				IF done THEN
					LEAVE loop_start;
				END IF;
                call reEvaluateOrder(orderID,counter);
				END LOOP loop_start;
		CLOSE myCursor;
    end;
	insert into orderReEvaluatedLog(orderReEvaluated) values(counter);
END //
DELIMITER ;