use bookstore;

-- Inser `appUser` table
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER1', 'khoa', '2000-12-12', '211 Ly Thuong Kiet', '0932758467', 'khoa.liang.nqk.demo@gmail.com', '$2y$10$i02L1Lm2q0EgMmC/Kn0P4.wu.on0YHz0O7kP7bFlKJfj.2pmyT7yC'); -- password: #Khoa123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER2', 'quang', '2002-12-12', '211 Ly Thuong Kiet', '0932758489', 'quang.nguyen.nqk.demo@gmail.com', '$2y$10$kTOjZHdB0QtOHwSmY8zhHe8qXGSmS.Zy1Xqkd4Qm9UpKQj9goxAyW'); -- password: #Quang123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('CUSTOMER3', 'nghia', '2002-6-12', '211 Ly Thuong Kiet', '0932758421', 'nghia.duong.nqk.demo@gmail.com', '$2y$10$3YI2xOzAgOJk6MM7RjWSYuP3yVFjatU3iWXR.gMpMp1OEKir.sbCe'); -- password: #Nghia123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('ADMIN1', 'admin1', '2002-6-12', '211 Ly Thuong Kiet', '0932758512', 'admin1.manager.nqk.demo@gmail.com', '$2y$10$yx/MyymkEUBh//fN6VmjCuNOXcMXYbMBUUcUKWUx.N.bcWpfq4vzS'); -- password: #Admin123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('ADMIN2', 'admin2', '2002-6-12', '211 Ly Thuong Kiet', '0932758874', 'admin2.manager.nqk.demo@gmail.com', '$2y$10$W97eotcF/htqkI3xA.0i9usQqaiGgKdd6oEMnEvkzT5auwaZLN3/e'); -- password: #Admin123456789

-- Insert `admin` table
INSERT INTO admin (`id`) VALUES ('ADMIN1');
INSERT INTO admin (`id`) VALUES ('ADMIN2');

-- Insert `customer` table
insert into customer (id,referrer,point,cardNumber) values('CUSTOMER1',null,0,1142369875),('CUSTOMER2','CUSTOMER1',0,1245369870),('CUSTOMER3','CUSTOMER1',0,4100335874);

-- Insert `book` table
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK1', 'The Joy of PHP', '1', '978494267353', '3', '4', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-13', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK2', 'Models: Attract Women Through Honesty', '3', '978494268253', '13', '4.7', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-30', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK3', 'Lord of Goblins, Vol. 1 Definitive Edition (Lord of Goblins', '1', '979889930075', '3', '4.5', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2023-6-8', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK4', 'The Hitchhiker`s Guide to the Galaxy', '50', '9780575096925', '3', '4.2', 'Pan Macmillan', 'https://www.panmacmillan.com/', '1979-10-12', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK5', 'The Time Machine', 1, '9780141975863', '12', 4.3, 'Penguin Classics', 'https://www.penguin.com/', '1895-01-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK6', 'Pride and Prejudice', 3, '9780143424680', '12', 4.6, 'Penguin Classics', 'https://www.penguin.com/', '1813-01-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK7', 'To Kill a Mockingbird', 50, '9780143284923', '10', 4.7, 'HarperCollins', 'https://www.harpercollins.com/', '1960-07-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK8', 'The Alchemist', 11, '9780671035193', '13', 4.2, 'HarperCollins', 'https://www.harpercollins.com/', '1988-08-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK9', 'The Handmaid`s Tale', 40, '9780312455030', '16', 4.5, 'Penguin Random House', 'https://www.penguinrandomhouse.com/', '1985-07-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK10', 'Sapiens: A Brief History of Humankind', 2, '9780316247110', '13', 4.4, 'Penguin Random House', 'https://www.penguinrandomhouse.com/', '2014-02-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK11', 'Dracula', 2, '9780062862684', '13', 4.1, 'Penguin Classics', 'https://www.penguin.com/', '1897-05-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK12', 'One Hundred Years of Solitude', 50, '9780385082786', '16', 4.8, 'Vintage Books', 'https://www.penguinrandomhouse.com/brands/vintage', '1967-05-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK13', 'The Martian', 3, '9780552165605', '10', 4.5, 'Crown Books', 'https://www.crownpublishing.com/', '2011-09-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK14', 'The Fault in Our Stars', 4, '9780312360557', '12', 4.6, 'Penguin Books', 'https://www.penguin.com/', '2012-01-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK15', 'Things Fall Apart', 3, '9780385470940', '13', 4.3, 'Anchor Books', 'https://www.penguinrandomhouse.com/brands/anchor', '1958-08-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK16', 'Murder on the Orient Express', 5, '9780062862700', '12', 4.4, 'HarperCollins', 'https://www.harpercollins.com/', '1934-01-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK17', 'Dune', 3, '9781631170673', '13', 4.7, 'BOOM! Studios', 'https://www.boom-studios.com/', '2020-01-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK18', 'The Lord of the Rings', 7, '9780395074673', '12', 4.9, 'Houghton Mifflin Harcourt', 'https://www.hmhbooks.com/', '1954-09-02', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK19', 'The Handmaid`s Tale', 5, '9781524795031', '16', 4.3, 'Tundra Books', 'https://www.tundrapublishing.com/', '2019-09-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK20', 'Sapiens: A Brief History of Humankind', 7, '9780525522176', '13', 4.2, 'Zest Books', 'https://www.zestbooks.com/', '2020-10-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK21', 'The Count of Monte Cristo', 5, '9780140435567', '13', 4.5, 'Penguin Classics', 'https://www.penguin.com/', '1844-08-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK22', 'Frankenstein', 14, '9780143538887', '10', 4.4, 'SparkNotes', 'https://www.sparknotes.com/', '1818-01-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK23', 'The Hitchhiker`s Guide to the Galaxy', 7, '9781623095605', '12', 4.7, 'Titan Comics', 'https://titan-comics.com/', '2019-08-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK24', '1984', 8, '9780743274920', '16', 4.8, 'Penguin Classics', 'https://www.penguin.com/', '1949-06-08', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK25', 'The God of Small Things', 6, '9780312642677', '16', 4.6, 'Vintage Books', 'https://www.penguinrandomhouse.com/brands/vintage', '1997-08-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK26', 'Echoes of Asgard', '3', '9781524598230', '12', '4.6', 'Candlewick Press', 'https://www.candlewick.com/', '2008-10-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK27', 'Papercut Dreams', '4', '9780312357891', '13', '4.5', 'Little, Brown and Company', 'https://www.littlebrown.com/', '2007-03-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK28', 'Code Red', '1', '9781416590217', '14', '4.7', 'HarperCollins Publishers', 'https://www.harpercollins.com/', '2009-07-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK29', 'Whisper in the Wilds', '3', '9780765384107', '10', '4.4', 'Macmillan Children`s Publishing Group', 'https://us.macmillan.com/', '2004-05-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK30', 'The Girl with the Timekeeper`s Heart', '4', '9781442347092', '13', '4.6', 'Simon & Schuster Children`s', 'https://www.simonandschuster.com/', '2006-11-01', '1');

-- Insert `author` table
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('AUTHOR3', '1', 'Michiel Werbrouck', 'https://www.amazon.com/stores/Michiel-Werbrouck/author/B089GQ8TC2?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');

-- Insert `customerorder` table
INSERT INTO `bookstore`.`customerorder` (`id`, `time`, `status`, `totalCost`, `totalDiscount`, `customerID`) VALUES ('ORDER1', '2023-06-15 09:30:00', '1', '300000.546', '10', 'CUSTOMER1');

-- Insert `eventdiscount` table
INSERT INTO `bookstore`.`eventdiscount` (`id`, `discount`, `startDate`, `endDate`, `applyForAll`) VALUES ('EVENT1', '10', '2023-12-31', '2024-1-31', '1');

-- Insert `eventapply` table
INSERT INTO `bookstore`.`eventapply` (`eventID`, `bookID`) VALUES ('EVENT1', 'BOOK1');
