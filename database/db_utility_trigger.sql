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