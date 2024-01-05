use bookstore;

-- Inser `appUser` table
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER1', 'khoa', '2000-12-12', '211 Ly Thuong Kiet', '0932758467', 'khoa.liang@gmail.com', '$2y$10$5SAXqmn0wLiugeIqYclLXO6HCSPM/Moicq/eenL14nbGkFuKA4f9m'); -- password: #Khoa123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER2', 'quang', '2002-12-12', '211 Ly Thuong Kiet', '0932758489', 'quang.nguyen@gmail.com', '$2y$10$RfbYSaYoaACxDIlSV.CHHO8obfABxCKfoPGR4I0AxQpX1Hutl6AEK'); -- password: #Quang123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER3', 'nghia', '2002-6-12', '211 Ly Thuong Kiet', '0932758421', 'nghia.duong@gmail.com', '$2y$10$oiIsoJitPRbIjQuTaHUQROBrSYAMA18rtlcTAaQT8SSRIF1F22J5i'); -- password: #Nghia123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('ADMIN1', 'admin1', '2002-6-12', '211 Ly Thuong Kiet', '0932758512', 'admin1.manager@gmail.com', '$2y$10$HzVbNRhajhZV2VlxS1WzW.AkRAokrsayk4VtPoZx2mqD0liEOaxYy'); -- password: #Admin123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('ADMIN2', 'admin2', '2002-6-12', '211 Ly Thuong Kiet', '0932758874', 'admin2.manager@gmail.com', '$2y$10$pY3wHPACqzi38mi46dq2HeyiqoIZgbBYMIgv4xIsAeWdq6mAR7pjG'); -- password: #Admin123456789

-- Insert `admin` table
INSERT INTO admin (`id`) VALUES ('ADMIN1');
INSERT INTO admin (`id`) VALUES ('ADMIN2');

-- Insert `customer` table
insert into customer (id,referrer,point,cardNumber) values('CUSTOMER1',null,0,1142369875),('CUSTOMER2','CUSTOMER1',0,1245369870),('CUSTOMER3','CUSTOMER1',0,4100335874);


-- insert book
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK3', 'Lord of Goblins, Vol. 1 Definitive Edition (Lord of Goblins', '1', ' 979889930075', '3', '4.5', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2023-6-8', '1');

INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK2', 'Models: Attract Women Through Honesty', '3', ' B00C93Q5KK', '13', '4.7', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-30', '1');

INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK1', 'The Joy of PHP', '1', ' 978494267353', '3', '4', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-13', '1');


-- insert author
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('AUTHOR3', '1', 'Michiel Werbrouck', 'https://www.amazon.com/stores/Michiel-Werbrouck/author/B089GQ8TC2?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');
