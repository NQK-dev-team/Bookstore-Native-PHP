use bookstore;

drop trigger if exists authorInsertUltilityTrigger;
delimiter //
create trigger authorInsertUltilityTrigger
before insert on author
for each row
begin
    declare newIndex int default 0;
    select authorIdx into newIndex from author where bookID=new.bookID order by authorIdx desc limit 1;
    set newIndex=newIndex+1;
    set new.authorIdx=newIndex;
end//
delimiter ;

drop trigger if exists categoryInsertUltilityTrigger;
delimiter //
create trigger categoryInsertUltilityTrigger
before insert on category
for each row
begin
    declare counter int default 0;
    select cast(substr(id,9) as unsigned) into counter from category ORDER BY cast(substr(id,9) as unsigned) DESC LIMIT 1;
    set counter=counter+1;
    set new.id=concat('CATEGORY',counter);
end//
delimiter ;