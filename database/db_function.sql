use bookstore;

drop function if exists generateRandomString;
DELIMITER //
CREATE FUNCTION generateRandomString() RETURNS VARCHAR(16) DETERMINISTIC
BEGIN
  DECLARE result VARCHAR(16) DEFAULT '';
  DECLARE all_chars VARCHAR(36) DEFAULT 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  DECLARE i INT DEFAULT 0;
  WHILE i < 16 DO
    SET result = CONCAT(result, SUBSTRING(all_chars, FLOOR(RAND() * CHAR_LENGTH(all_chars)) + 1, 1));
    SET i = i + 1;
  END WHILE;
  RETURN result;
END//
DELIMITER ;