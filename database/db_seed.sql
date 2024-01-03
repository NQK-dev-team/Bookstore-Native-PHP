-- insert user

INSERT INTO `bookstore`.`user` (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER1', 'khoa', '2000-12-12', '211 Ly Thuong Kiet', '0932758467', 'khoa.liang.business@gmail.com', '12345678');
INSERT INTO `bookstore`.`user` (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER2', 'quang', '2002-12-12', '211 Ly Thuong Kiet', '0932758489', 'quang.liang.business@gmail.com', '12345678');
INSERT INTO `bookstore`.`user` (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER3', 'nghia', '2002-6-12', '211 Ly Thuong Kiet', '0932758421', 'nghia.liang.business@gmail.com', '12345678');


-- insert book
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK3', 'Lord of Goblins, Vol. 1 Definitive Edition (Lord of Goblins', '1', ' 979889930075', '3', '4.5', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2023-6-8', '1');

INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK2', 'Models: Attract Women Through Honesty', '3', ' B00C93Q5KK', '13', '4.7', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-30', '1');

INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK1', 'The Joy of PHP', '1', ' 978494267353', '3', '4', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-13', '1');

-- insert admin
INSERT INTO `bookstore`.`admin` (`id`) VALUES ('ADMIN1');
INSERT INTO `bookstore`.`admin` (`id`) VALUES ('ADMIN2');


-- insert author
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('AUTHOR3', '1', 'Michiel Werbrouck', 'https://www.amazon.com/stores/Michiel-Werbrouck/author/B089GQ8TC2?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');
