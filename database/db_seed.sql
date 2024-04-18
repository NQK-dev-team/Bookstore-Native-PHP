use bookstore;

-- Inser `appUser` table
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER1', 'luong anh khoa', '2000-12-12', '211 Ly Thuong Kiet', '0932758467', 'khoa.liang.nqk.demo@gmail.com', '$2y$10$i02L1Lm2q0EgMmC/Kn0P4.wu.on0YHz0O7kP7bFlKJfj.2pmyT7yC','M'); -- password: #Khoa123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER2', 'nguyen minh quang', '2002-12-12', '211 Ly Thuong Kiet', '0932758489', 'quang.nguyen.nqk.demo@gmail.com', '$2y$10$kTOjZHdB0QtOHwSmY8zhHe8qXGSmS.Zy1Xqkd4Qm9UpKQj9goxAyW','M'); -- password: #Quang123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER3', 'duong nguyen nguyen nghia', '2002-6-12', '211 Ly Thuong Kiet', '0932758421', 'nghia147ty@gmail.com', '$2y$10$3YI2xOzAgOJk6MM7RjWSYuP3yVFjatU3iWXR.gMpMp1OEKir.sbCe','M'); -- password: #Nghia123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('ADMIN1', 'admin1', '2002-6-12', '211 Ly Thuong Kiet', '0932758512', 'admin1.manager.nqk.demo@gmail.com', '$2y$10$yx/MyymkEUBh//fN6VmjCuNOXcMXYbMBUUcUKWUx.N.bcWpfq4vzS','M'); -- password: #Admin123456789
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('ADMIN2', 'admin2', '2002-6-12', '211 Ly Thuong Kiet', '0932758874', 'admin2.manager.nqk.demo@gmail.com', '$2y$10$W97eotcF/htqkI3xA.0i9usQqaiGgKdd6oEMnEvkzT5auwaZLN3/e','M'); -- password: #Admin123456789

INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER4', 'test1', '2002-1-12', '211 Ly Thuong Kiet', '0032758421', 'test1@gmail.com', 'test123456789','M'); -- used for testing the list of customer feature only
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER5', 'test2', '2002-2-28', '211 Ly Thuong Kiet', '0193275841', 'test2@gmail.com', 'test123456789','M'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER6', 'test3', '2000-2-29', '211 Ly Thuong Kiet', '0232758421', 'test3@gmail.com', 'test123456789','F'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER7', 'test4', '2002-3-12', '211 Ly Thuong Kiet', '0332758421', 'test4@gmail.com', 'test123456789','F'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER8', 'test5', '2002-5-12', '211 Ly Thuong Kiet', '0432758421', 'test5@gmail.com', 'test123456789','O'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER9', 'test6', '2002-1-12', '211 Ly Thuong Kiet', '0532758421', 'test6@gmail.com', 'test123456789','O'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER10', 'test7', '2002-1-12', null, null, null, null,'M'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER11', 'test8', '2002-1-12', null, null, null, null,'M'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER12', 'test9', '2002-1-12', null, null, null, null,'F'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER13', 'test10', '2002-1-12', null, null, null, null,'F'); -- used for testing the list of customer featureonly
INSERT INTO appUser (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`, `gender`) VALUES ('CUSTOMER14', 'test11', '2002-1-12', null, null, null, null,'O'); -- used for testing the list of customer featureonly

-- Insert `admin` table
INSERT INTO admin (`id`) VALUES ('ADMIN1');
INSERT INTO admin (`id`) VALUES ('ADMIN2');

-- Insert `customer` table
insert into customer (id,referrer,point) values('CUSTOMER1',null,27.31),('CUSTOMER2','CUSTOMER1',27.87),('CUSTOMER3','CUSTOMER1',27.87);
insert into customer (id,referrer,point,status,deleteTime) values('CUSTOMER4','CUSTOMER1',0,true,null),('CUSTOMER5',null,0,true,null),('CUSTOMER6',null,0,true,null),('CUSTOMER7',null,0,true,null),('CUSTOMER8',null,0,true,null),('CUSTOMER9',null,0,false,'2024-01-31 13:00:00'),
('CUSTOMER10',null,0,false,null),('CUSTOMER11',null,0,false,null),('CUSTOMER12',null,0,false,null),('CUSTOMER13',null,0,false,null),('CUSTOMER14',null,0,false,null);

-- Insert `book` table
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK1', 'The Joy of PHP', '1', '9781522792147', '4', 'CreateSpace Independent Publishing Platform', '2012-12-13', '1','demo/BOOK1/The Joy of PHP.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK2', 'Models: Attract Women Through Honesty', '3', '9781463750350', '4.7', 'CreateSpace Independent Publishing Platform', '2012-12-30', '1','demo/BOOK2/Models Attract Women Through Honesty.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK3', 'Lord of Goblins, Vol. 1 Definitive Edition (Lord of Goblins)', '1', '9798889930075', '4.5', 'CreateSpace Independent Publishing Platform', '2023-6-8', '1','demo/BOOK3/Lord of Goblins, Vol. 1 Definitive Edition (Lord of Goblins).png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK4', 'The Hitchhiker\'s Guide to the Galaxy', '50', '9780575096925', '4.2', 'Pan Macmillan', '1979-10-12', '1','demo/BOOK4/The Hitchhiker\'s Guide to the Galaxy.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK5', 'The Time Machine', 1, '9780141975863', 4.3, 'Penguin Classics', '1895-01-01', '1','demo/BOOK5/The Time Machine.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK6', 'Pride and Prejudice', 3, '9780143424680', 4.6, 'Penguin Classics', '1813-01-01', '1','demo/BOOK6/Pride and Prejudice.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK7', 'To Kill a Mockingbird', 50, '9780143284923', 4.7, 'HarperCollins', '1960-07-01', '1','demo/BOOK7/To Kill a Mockingbird.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK8', 'The Alchemist', 11, '9780671035193', 4.2, 'HarperCollins', '1988-08-01', '1','demo/BOOK8/The Alchemist.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK9', 'The Handmaid\'s Tale', 40, '9780312455030', 4.5, 'Penguin Random House', '1985-07-01', '1','demo/BOOK9/The Handmaid\'s Tale.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK10', 'Sapiens: A Brief History of Humankind', 2, '9780316247110', 4.4, 'Penguin Random House', '2014-02-01', '1','demo/BOOK10/Sapiens A Brief History of Humankind.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK11', 'Dracula', 2, '9780062862684', 4.1, 'Penguin Classics', '1897-05-01', '1','demo/BOOK11/Dracula.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK12', 'One Hundred Years of Solitude', 50, '9780385082786', 4.8, 'Vintage Books', '1967-05-01', '1','demo/BOOK12/One Hundred Years of Solitude.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK13', 'The Martian', 3, '9780552165605', 4.5, 'Crown Books', '2011-09-01', '1','demo/BOOK13/The Martian.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK14', 'The Fault in Our Stars', 4, '9780312360557', 4.6, 'Penguin Books', '2012-01-01', '1','demo/BOOK14/The Fault in Our Stars.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK15', 'Things Fall Apart', 3, '9780385470940', 4.3, 'Anchor Books', '1958-08-01', '1','demo/BOOK15/Things Fall Apart.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK16', 'Murder on the Orient Express', 5, '9780062862700', 4.4, 'HarperCollins', '1934-01-01', '1','demo/BOOK16/Murder on the Orient Express.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK17', 'Dune', 3, '9781631170673', 4.7, 'BOOM! Studios', '2020-01-01', '1','demo/BOOK17/Dune.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK18', 'The Lord of the Rings', 7, '9780395074673', 4.9, 'Houghton Mifflin Harcourt', '1954-09-02', '1','demo/BOOK18/The Lord of the Rings.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK19', 'The Handmaid\'s Tale', 5, '9781524795031', 4.3, 'Tundra Books', '2019-09-01', '1','demo/BOOK19/The Handmaid\'s Tale.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK20', 'Sapiens: A Brief History of Humankind', 7, '9780525522176', 4.2, 'Zest Books', '2020-10-01', '1','demo/BOOK20/Sapiens A Brief History of Humankind.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK21', 'The Count of Monte Cristo', 5, '9780140435567', 4.5, 'Penguin Classics', '1844-08-01', '1','demo/BOOK21/The Count of Monte Cristo.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK22', 'Frankenstein', 14, '9780143538887', 4.4, 'SparkNotes', '1818-01-01', '1','demo/BOOK22/Frankenstein.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK23', 'The Hitchhiker\'s Guide to the Galaxy', 7, '9781623095605', 4.7, 'Titan Comics', '2019-08-01', '1','demo/BOOK23/The Hitchhiker\'s Guide to the Galaxy.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK24', '1984', 8, '9780743274920', 4.8, 'Penguin Classics', '1949-06-08', '1','demo/BOOK24/1984.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK25', 'The God of Small Things', 6, '9780312642677', 4.6, 'Vintage Books', '1997-08-01', '1','demo/BOOK25/The God of Small Things.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK26', 'Echoes of Asgard', '3', '9781524598230', '4.6', 'Candlewick Press', '2008-10-01', '1','demo/BOOK26/Echoes of Asgard.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK27', 'Money Magic', '4', '9780312357891', '4.5', 'Little, Brown and Company', '2007-03-01', '1','demo/BOOK27/Money Magic.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK28', 'Code Red', '1', '9781416590217', '4.7', 'HarperCollins Publishers', '2009-07-01', '1','demo/BOOK28/Code Red.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK29', 'Whisper in the Wilds', '3', '9780765384107', '4.4', 'Macmillan Children\'s Publishing Group', '2004-05-01', '1','demo/BOOK29/Whisper in the Wilds.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK30', 'The Girl with the Timekeeper\'s Heart', '4', '9781442347092', '4.6', 'Simon & Schuster Children\'s', '2006-11-01', '1','demo/BOOK30/The Girl with the Timekeeper\'s Heart.png');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `avgRating`, `publisher`, `publishDate`, `status`, `imagePath`) VALUES ('BOOK31', 'Lord of Goblins, Vol. 2', '3', '9780765384297', '4.5', 'MoonQuill', '2023-04-30', '1','demo/BOOK31/Lord of Goblins, Vol. 2.png');

-- Insert `author` table
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK1', '1', 'Alan Forbes');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK2', '1', 'Mark Manson');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK3', '1', 'Michiel Werbrouck');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK4', '1', 'Douglas Adams');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK5', '1', 'Herbert George Wells');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK6', '1', 'Jane Austen');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK7', '1', 'Harper Lee');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK8', '1', 'Paulo Coelho');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK9', '1', 'Margaret Atwood');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK10', '1', 'Yuval Noah Harari');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK11', '1', 'Bram Stoker');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK12', '1', 'Gabriel García Márquez');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK13', '1', 'Andy Weir');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK14', '1', 'John Green');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK15', '1', 'Chinua Achebe');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK16', '1', 'Agatha Christie');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK17', '1', 'Frank Herbert');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK18', '1', 'John Ronald Reuel Tolkien');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK19', '1', 'Margaret Atwood');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK20', '1', 'Yuval Noah Harari');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK21', '1', 'Alexandre Dumas');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK22', '1', 'Mary Shelley');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK23', '1', 'Douglas Adams');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK24', '1', 'George Orwell');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK25', '1', 'Arundhati Roy');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK26', '1', 'Gaia Sol');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK27', '1', 'Laurence Kotlikoff');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK28', '1', 'Kyle Mills');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK29', '1', 'Adam Shoalts');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK30', '1', 'Mitch Albom');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`) VALUES ('BOOK31', '1', 'Michiel Werbrouck');

-- Insert `category`
INSERT INTO `bookstore`.`category` (`id`, `name`,`description`) VALUES
  ('CATEGORY1','Fiction','Fiction books are imaginative narratives that take readers into invented worlds, offering an entertaining escape into diverse emotions and experiences.'),
  ('CATEGORY2','Fantasy','Fantasy books weave magical tales, transporting readers to enchanting worlds filled with mythical creatures and extraordinary adventures.'),
  ('CATEGORY3','Mystery','Mystery books captivate with suspenseful tales, inviting readers to unravel puzzles and navigate thrilling plots, often featuring detectives and unexpected twists.'),
  ('CATEGORY4','Thriller','Thriller books offer gripping plots, high stakes, and relentless suspense, delivering an edge-of-the-seat reading experience.'),
  ('CATEGORY5','Romance','Romance books delve into love and relationships, weaving passionate tales of connection and heartwarming journeys to find true love.'),
  ('CATEGORY6','Historical fiction','Historical fiction brings the past to life, blending real history with fictional characters and events, creating captivating stories set against rich historical backdrops.'),
  ('CATEGORY7','Science fiction','Science fiction envisions futuristic worlds, probing into advanced technologies, extraterrestrial encounters, and the limitless possibilities of the universe, sparking curiosity and creativity.'),
  ('CATEGORY8','Nonfiction','Nonfiction books provide factual insights and real-world knowledge across diverse subjects, offering readers an opportunity to expand their understanding of the world.'),
  ('CATEGORY9','Biography','Biographies offer firsthand insights into the lives, achievements, and challenges of real individuals, providing readers with a glimpse into notable figures\' personal journeys.'),
  ('CATEGORY10','Memoir','Memoirs share personal experiences and emotions, offering an intimate journey through the author\'s life, capturing significant moments and unique perspectives.'),
  ('CATEGORY11','History','History books chronicle past events and societal changes, offering insights into the triumphs and challenges of human development over time.'),
  ('CATEGORY12','Self-help','Self-help books empower readers with practical tools and insights for personal development, offering actionable advice for positive life changes and growth.'),
  ('CATEGORY13','Business','Business books delve into the principles, challenges, and trends of the corporate world, offering valuable insights for individuals navigating the dynamic landscape of business and entrepreneurship.'),
  ('CATEGORY14','Children\'s books','Children\'s books captivate young readers with imaginative stories, vibrant illustrations, and valuable life lessons, fostering a love for reading and nurturing creativity.'),
  ('CATEGORY15','Tutorial','Tutorials offer step-by-step guidance for learners to acquire new skills or knowledge in a hands-on and practical manner.');

-- Insert `belong`
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK1', 'CATEGORY15');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK1', 'CATEGORY8');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK2', 'CATEGORY8');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK2', 'CATEGORY12');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK2', 'CATEGORY5');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK3', 'CATEGORY1');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK3', 'CATEGORY2');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK4', 'CATEGORY7');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK4', 'CATEGORY4');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK5', 'CATEGORY7');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK5', 'CATEGORY14');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK6', 'CATEGORY6');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK7', 'CATEGORY7');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK8', 'CATEGORY2');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK9', 'CATEGORY6');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK10', 'CATEGORY11');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK11', 'CATEGORY6');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK12', 'CATEGORY7');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK13', 'CATEGORY7');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK14', 'CATEGORY8');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK15', 'CATEGORY3');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK16', 'CATEGORY1');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK17', 'CATEGORY7');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK18', 'CATEGORY2');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK19', 'CATEGORY6');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK20', 'CATEGORY11');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK21', 'CATEGORY6');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK22', 'CATEGORY2');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK23', 'CATEGORY1');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK24', 'CATEGORY6');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK25', 'CATEGORY3');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK26', 'CATEGORY1');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK27', 'CATEGORY13');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK28', 'CATEGORY1');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK29', 'CATEGORY2');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK30', 'CATEGORY1');
INSERT INTO `bookstore`.`belong` (`bookID`, `categoryID`) VALUES ('BOOK31', 'CATEGORY2');

-- Insert `physical copy`
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK1', '29.99', '2');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK2', '39.99', '14');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK3', '49.99', '14');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK4', '59.99', '32');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK5', '15.99', '15');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK6', '29.99', '1');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK7', '39.99', '15');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK8', '49.99', '18');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK9', '59.99', '11');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK10', '15.99', '1');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK11', '29.99', '25');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK12', '39.99', '19');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK13', '49.99', '4');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK14', '59.99', '15');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK15', '15.99', '20');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK16', '29.99', '17');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK17', '39.99', '21');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK18', '49.99', '14');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK19', '59.99', '10');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK20', '15.99', '7');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK21', '29.99', '15');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK22', '39.99', '25');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK23', '49.99', '14');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK24', '59.99', '12');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK25', '15.99', '17');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK26', '29.99', '24');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK27', '39.99', '14');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK28', '49.99', '19');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK29', '59.99', '1');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK30', '15.99', '15');
INSERT INTO `bookstore`.`physicalcopy` (`id`, `price`, `inStock`) VALUES ('BOOK31', '29.99', '18');

-- Insert file copy
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK1', '13.99', 'demo/BOOK1/The Joy of PHP.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK2', '13.99', 'demo/BOOK2/Models Attract Women Through Honesty.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK3', '13.99', 'demo/BOOK3/Lord of Goblins, Vol. 1 Definitive Edition (Lord of Goblins).pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK4', '13.99', 'demo/BOOK4/The Hitchhiker\'s Guide to the Galaxy.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK5', '13.99', 'demo/BOOK5/The Time Machine.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK6', '13.99', 'demo/BOOK6/Pride and Prejudice.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK7', '13.99', 'demo/BOOK7/To Kill a Mockingbird.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK8', '13.99', 'demo/BOOK8/The Alchemist.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK9', '13.99', 'demo/BOOK9/The Handmaid\'s Tale.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK10', '13.99', 'demo/BOOK10/Sapiens A Brief History of Humankind.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK11', '13.99', 'demo/BOOK11/Dracula.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK12', '13.99', 'demo/BOOK12/One Hundred Years of Solitude.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK13', '13.99', 'demo/BOOK13/The Martian.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK14', '13.99', 'demo/BOOK14/The Fault in Our Stars.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK15', '13.99', 'demo/BOOK15/Things Fall Apart.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK16', '13.99', 'demo/BOOK16/Murder on the Orient Express.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK17', '13.99', 'demo/BOOK17/Dune.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK18', '13.99', 'demo/BOOK18/The Lord of the Rings.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK19', '13.99', 'demo/BOOK19/The Handmaid\'s Tale.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK20', '13.99', 'demo/BOOK20/Sapiens A Brief History of Humankind.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK21', '13.99', 'demo/BOOK21/The Count of Monte Cristo.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK22', '13.99', 'demo/BOOK22/Frankenstein.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK23', '13.99', 'demo/BOOK23/The Hitchhiker\'s Guide to the Galaxy.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK24', '13.99', 'demo/BOOK24/1984.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK25', '13.99', 'demo/BOOK25/The God of Small Things.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK26', '13.99', 'demo/BOOK26/Echoes of Asgard.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK27', '13.99', 'demo/BOOK27/Money Magic.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK28', '13.99', 'demo/BOOK28/Code Red.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK29', '13.99', 'demo/BOOK29/Whisper in the Wilds.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK30', '13.99', 'demo/BOOK30/The Girl with the Timekeeper\'s Heart.pdf');
INSERT INTO `bookstore`.`filecopy` (`id`, `price`, `filePath`) VALUES ('BOOK31', '13.99', 'demo/BOOK31/Lord of Goblins, Vol. 2.pdf');

-- ADD discription to book
UPDATE `bookstore`.`book` SET `description` = 'Have you ever wanted to design your own website or browser application but thought it would be too difficult or maybe you didn\'t know where to start? Have you found the amount of information on the Internet either too overwhelming or not geared for your skill set or worse-- just plain boring? Are you interested in learning to program PHP and have some fun along the way? If so, then The Joy of PHP by Alan Forbes is the book for you!!' WHERE (`id` = 'BOOK1');
UPDATE `bookstore`.`book` SET `description` = 'From a renowned historian comes a groundbreaking narrative of humanity’s creation and evolution—a #1 international bestseller—that explores the ways in which biology and history have defined us and enhanced our understanding of what it means to be “human.”' WHERE (`id` = 'BOOK10');
UPDATE `bookstore`.`book` SET `description` = 'Dracula is an 1897 Gothic horror novel by Irish author Bram Stoker.Famous for introducing the character of the vampire Count Dracula, the novel tells the story of Dracula\'s attempt to move from Transylvania to England so he may find new blood and spread undead curse, and the battle between Dracula and a small group of men and women led by Professor Abraham Van Helsing.' WHERE (`id` = 'BOOK11');
UPDATE `bookstore`.`book` SET `description` = 'The novel tells the story of the rise and fall of the mythical town of Macondo through the history of the Buendía family. Rich and brilliant, it is a chronicle of life, death, and the tragicomedy of humankind. In the beautiful, ridiculous, and tawdry story of the Buendía family, one sees all of humanity, just as in the history, myths, growth, and decay of Macondo, one sees all of Latin America.' WHERE (`id` = 'BOOK12');
UPDATE `bookstore`.`book` SET `description` = 'Wil Wheaton, who has lent his voice to sci-fi blockbusters like Ready Player One and Redshirts, breathes new life (and plenty of sarcasm) into the iconic character of Mark Watney, making this edition a must-listen for both longtime fans of The Martian and new listeners alike.' WHERE (`id` = 'BOOK13');
UPDATE `bookstore`.`book` SET `description` = 'Despite the tumor-shrinking medical miracle that has bought her a few years, Hazel has never been anything but terminal, her final chapter inscribed upon diagnosis. But when a gorgeous plot twist named Augustus Waters suddenly appears at Cancer Kid Support Group, Hazel’s story is about to be completely rewritten.' WHERE (`id` = 'BOOK14');
UPDATE `bookstore`.`book` SET `description` = 'How can we live our lives when everything seems to fall apart—when we are continually overcome by fear, anxiety, and pain? The answer, Pema Chödrön suggests, might be just the opposite of what you expect. Here, in her most beloved and acclaimed work, Pema shows that moving toward painful situations and becoming intimate with them can open up our hearts in ways we never before imagined. Drawing from traditional Buddhist wisdom, she offers life-changing tools for transforming suffering and negative patterns into habitual ease and boundless joy.' WHERE (`id` = 'BOOK15');
UPDATE `bookstore`.`book` SET `description` = 'Just after midnight, the famous Orient Express is stopped in its tracks by a snowdrift. By morning, the millionaire Samuel Edward Ratchett lies dead in his compartment, stabbed a dozen times, his door locked from the inside. One of his fellow passengers must be the murderer.' WHERE (`id` = 'BOOK16');
UPDATE `bookstore`.`book` SET `description` = 'Set on the desert planet Arrakis, Dune is the story of the boy Paul Atreides, who would become the mysterious man known as Maud\'dib. He would avenge the traitorous plot against his noble family - and would bring to fruition humankind\'s most ancient and unattainable dream.' WHERE (`id` = 'BOOK17');
UPDATE `bookstore`.`book` SET `description` = 'In a sleepy village in the Shire, a young hobbit is entrusted with an immense task. He must make a perilous journey across Middle-earth to the Cracks of Doom, there to destroy the Ruling Ring of Power - the only thing that prevents the Dark Lord Sauron’s evil dominion.' WHERE (`id` = 'BOOK18');
UPDATE `bookstore`.`book` SET `description` = 'Margaret Atwood\'s popular dystopian novel The Handmaid\'s Tale explores a broad range of issues relating to power, gender, and religious politics. Multiple Golden Globe award-winner Claire Danes (Romeo and Juliet, The Hours) gives a stirring performance of this classic in speculative fiction, one of the most powerful and widely read novels of our time. ' WHERE (`id` = 'BOOK19');
UPDATE `bookstore`.`book` SET `description` = 'Models is the first book ever written on seduction as an emotional process rather than a logical one, a process of connecting with women rather than impressing them. It\'s the most mature and honest guide on how a man can attract women without faking behavior, without lying and without emulating others. A game-changer. ' WHERE (`id` = 'BOOK2');
UPDATE `bookstore`.`book` SET `description` = 'From a renowned historian comes a groundbreaking narrative of humanity’s creation and evolution—a #1 international bestseller—that explores the ways in which biology and history have defined us and enhanced our understanding of what it means to be “human.”' WHERE (`id` = 'BOOK20');
UPDATE `bookstore`.`book` SET `description` = 'On the eve of his marriage to the beautiful Mercedes, having that very day been made captain of his ship, the young sailor Edmond Dantès is arrested on a charge of treason, trumped up by jealous rivals. Incarcerated for many lonely years in the isolated and terrifying Chateau d\'If near Marseille, he meticulously plans his brilliant escape and extraordinary revenge.' WHERE (`id` = 'BOOK21');
UPDATE `bookstore`.`book` SET `description` = 'Narrator Dan Stevens (Downton Abbey) presents an uncanny performance of Mary Shelley\'s timeless gothic novel, an epic battle between man and monster at its greatest literary pitch. In trying to create life, the young student Victor Frankenstein unleashes forces beyond his control, setting into motion a long and tragic chain of events that brings Victor to the very brink of madness. How he tries to destroy his creation, as it destroys everything Victor loves, is a powerful story of love, friendship, scientific hubris, and horror.' WHERE (`id` = 'BOOK22');
UPDATE `bookstore`.`book` SET `description` = 'Seconds before the Earth is demolished to make way for a galactic freeway, Arthur Dent is plucked off the planet by his friend Ford Prefect, a researcher for the revised edition of The Hitchhiker\'s Guide to the Galaxy who, for the last 15 years, has been posing as an out-of-work actor.' WHERE (`id` = 'BOOK23');
UPDATE `bookstore`.`book` SET `description` = 'George Orwell\'s nineteen Eighty-Four is one of the most definitive texts of modern literature. Set in Oceania, one of the three inter-continental superstate that divided the world among themselves after a global war, Orwell\'s masterful critique of the political structures of the time, works itself out through the story of Winston Smith, a man caught in the webs of a dystopian future, and his clandestine love affair with Julia, a young woman he meets during the course of his work for the government. As much as it is an entertaining read, nineteen Eighty-Four is also a brilliant, and more importantly, a timeless satirical attack on the social and political structures of the world.' WHERE (`id` = 'BOOK24');
UPDATE `bookstore`.`book` SET `description` = 'Set against a background of political turbulence in Kerala, Southern India, The God of Small Things tells the story of twins Esthappen and Rahel. Amongst the vats of banana jam and heaps of peppercorns in their grandmother\'s factory, they try to craft a childhood for themselves amidst what constitutes their family - their lonely, lovely mother; their beloved uncle Chacko (pickle baron, radical Marxist and bottom pincher); and their avowed enemy, Baby Kochamma (ex-nun and incumbent grand-aunt).' WHERE (`id` = 'BOOK25');
UPDATE `bookstore`.`book` SET `description` = 'It is a dark time for Asgard. The All-Father is trapped in a bewitched Odinsleep, inspiring an all-out assault from the Frost Giants. They evade the gods’ defenses with uncommon ease, as if guided by augury. Heimdall, a quick-witted young warrior still finding his place amongst Asgard’s defenders, believes it no coincidence that Odin lies enchanted and that the Giants are so well-informed. Sneaking into Odin’s inner chambers, he discovers that the severed head of Mimir, a great source of wisdom, is missing. Accompanied by his sister, Lady Sif, Heimdall must quest across the Nine Realms to retrieve it, lest mighty Asgard fall.' WHERE (`id` = 'BOOK26');
UPDATE `bookstore`.`book` SET `description` = 'Laurence Kotlikoff, one of our nation’s premier personal finance experts and coauthor of the New York Times bestseller Get What’s Yours: The Secrets to Maxing Out Your Social Security, harnesses the power of economics and advanced computation to deliver a host of spellbinding but simple money magic tricks that will transform your financial future.Each trick shares a basic ingredient for financial savvy based on economic common sense, not Wall Street snake oil. Money Magic offers a clear path to a richer, happier, and safer financial life. Whether you’re making education, career, marriage, lifestyle, housing, investment, retirement, or Social Security decisions, Kotlikoff provides a clear framework for readers of all ages and income levels to learn tricks' WHERE (`id` = 'BOOK27');
UPDATE `bookstore`.`book` SET `description` = 'Mitch Rapp owes powerful criminal Damian Losa a favour, and it’s being called in. With no choice other than to honour his agreement, Rapp heads to Syria to stop a new designer drug spreading into Losa’s territory. When he discovers the true culprit – someone with far bigger goals than just Syria – the scale of his mission grows.' WHERE (`id` = 'BOOK28');
UPDATE `bookstore`.`book` SET `description` = 'A preeminent figure in faith-based fiction, Lauraine Snelling has over two million copies of her works in print. In Whispers in the Wind, the second installment of Snelling’s Wild West Wind series, Cassie Lockwood is dismayed to discover her father’s South Dakota valley land is already occupied. Meanwhile, Cassie’s arrival spurs the revelation of long-hidden secrets among the locals and leaves her questioning whether or not she will ever find a place to call home.' WHERE (`id` = 'BOOK29');
UPDATE `bookstore`.`book` SET `description` = 'Corruption. Greed. War. Lev is no stranger to the evils of the world, having spent a lifetime fighting the predatory system that ensnared the world\'s population and turned them into puppets for the leaders of humanity.' WHERE (`id` = 'BOOK3');
UPDATE `bookstore`.`book` SET `description` = 'Already optioned for film, The Girl with a Clock for a Heart is Peter Swanson\'s electrifying tale of romantic noir, with shades of Hitchcock and reminiscent of the classic movie Body Heat. It is the story of a man swept into a vortex of irresistible passion and murder when an old love mysteriously reappears.' WHERE (`id` = 'BOOK30');
UPDATE `bookstore`.`book` SET `description` = 'It should have been a simple expedition. Gather haze crystals, kill hivelings, and return to the caverns. That’s what it should have been, but rarely are things so simple. After falling into the depths of the lower floors, Lev and the remains of the vanguard must survive in uncharted terrain and find their way back to civilization. Along the way, an ancient being drags Lev deeper into the abyss. With promises of forgotten power, it sends Lev and his party even further into the annals of this world’s history.' WHERE (`id` = 'BOOK31');
UPDATE `bookstore`.`book` SET `description` = 'Seconds before the Earth is demolished to make way for a galactic freeway, Arthur Dent is plucked off the planet by his friend Ford Prefect, a researcher for the revised edition of The Hitchhiker\'s Guide to the Galaxy who, for the last 15 years, has been posing as an out-of-work actor.' WHERE (`id` = 'BOOK4');
UPDATE `bookstore`.`book` SET `description` = 'When a Victorian scientist propels himself into the year 802,701 AD, he is initially delighted to find that suffering has been replaced by beauty, contentment and peace. Entranced at first by the Eloi, an elfin species descended from man, he soon realises that this beautiful people are simply remnants of a once-great culture - now weak and childishly afraid of the dark. But they have every reason to be afraid: in deep tunnels beneath their paradise lurks another race descended from humanity - the sinister Morlocks. And when the scientist\'s time machine vanishes, it becomes clear he must search these tunnels, if he is ever to return to his own era.' WHERE (`id` = 'BOOK5');
UPDATE `bookstore`.`book` SET `description` = 'One of Jane Austen’s most beloved works, Pride and Prejudice, is vividly brought to life by Academy Award nominee Rosamund Pike (Gone Girl). In her bright and energetic performance of this British classic, she expertly captures Austen’s signature wit and tone. Her attention to detail, her literary background, and her performance in the 2005 feature film version of the novel provide the perfect foundation from which to convey the story of Elizabeth Bennet, her four sisters, and the inimitable Mr. Darcy. ' WHERE (`id` = 'BOOK6');
UPDATE `bookstore`.`book` SET `description` = 'One of the best-loved stories of all time, To Kill a Mockingbird has been translated into more than 40 languages, sold more than 30 million copies worldwide, served as the basis for an enormously popular motion picture, and was voted one of the best novels of the 20th century by librarians across the country. A gripping, heart-wrenching, and wholly remarkable tale of coming-of-age in a South poisoned by virulent prejudice, it views a world of great beauty and savage inequities through the eyes of a young girl, as her father - a crusading local lawyer - risks everything to defend a black man unjustly accused of a terrible crime.' WHERE (`id` = 'BOOK7');
UPDATE `bookstore`.`book` SET `description` = 'Paulo Coelho\'s enchanting novel has inspired a devoted following around the world. This story, dazzling in its simplicity and wisdom, is about an Andalusian shepherd boy named Santiago who travels from his homeland in Spain to the Egyptian desert in search of treasure buried in the Pyramids. Along the way he meets a Gypsy woman, a man who calls himself king, and an Alchemist, all of whom point Santiago in the direction of his quest. No one knows what the treasure is, or if Santiago will be able to surmount the obstacles along the way But what starts out as a journey to find worldly goods turns into a meditation on the treasures found within. Lush, evocative, and deeply humane, the story of Santiago is art eternal testament to the transforming power of our dreams and the importance of listening to our hearts.' WHERE (`id` = 'BOOK8');
UPDATE `bookstore`.`book` SET `description` = 'Margaret Atwood\'s popular dystopian novel The Handmaid\'s Tale explores a broad range of issues relating to power, gender, and religious politics. Multiple Golden Globe award-winner Claire Danes (Romeo and Juliet, The Hours) gives a stirring performance of this classic in speculative fiction, one of the most powerful and widely read novels of our time. ' WHERE (`id` = 'BOOK9');

insert into discount(id,name,status) values('C_DISCOUNT1','Customer discount level 1',true),('C_DISCOUNT2','Customer discount level 2',true),('C_DISCOUNT3','Customer discount level 3',true);
insert into discount(id,name,status) values('R_DISCOUNT1','Referrer discount level 1',true),('R_DISCOUNT2','Referrer discount level 2',true),('R_DISCOUNT3','Referrer discount level 3',true);
insert into discount(id,name,status) values('E_DISCOUNT1','Black Friday Sales',true),('E_DISCOUNT2','Science Fair',true),('E_DISCOUNT3','History Lesson',true),('E_DISCOUNT4','Children\'s story',true),('E_DISCOUNT5','Fiction day',true);

insert into customerDiscount(id,point,discount) values('C_DISCOUNT1',50,5),('C_DISCOUNT2',100,7),('C_DISCOUNT3',200,10);
insert into referrerDiscount(id,numberOfPeople,discount) values('R_DISCOUNT1',3,2),('R_DISCOUNT2',5,5),('R_DISCOUNT3',10,7);
insert into eventDiscount(id,discount,startDate,endDate,applyForAll) values('E_DISCOUNT1',30,date_sub(curdate(),interval 3 day),date_add(curdate(),interval 10 day),true),
('E_DISCOUNT2',35,date_sub(curdate(),interval 1 day),date_add(curdate(),interval 7 day),false),
('E_DISCOUNT3',25,date_sub(curdate(),interval 2 day),date_add(curdate(),interval 9 day),false),
('E_DISCOUNT4',40,date_sub(curdate(),interval 2 day),date_add(curdate(),interval 7 day),false),
('E_DISCOUNT5',20,date_sub(curdate(),interval 8 day),date_add(curdate(),interval 3 day),false);

insert into eventApply(eventID,bookID) values('E_DISCOUNT2','BOOK4'),('E_DISCOUNT2','BOOK5'),('E_DISCOUNT2','BOOK7'),('E_DISCOUNT2','BOOK12'),('E_DISCOUNT2','BOOK13'),('E_DISCOUNT2','BOOK17');
insert into eventApply(eventID,bookID) values('E_DISCOUNT3','BOOK10'),('E_DISCOUNT3','BOOK20');
insert into eventApply(eventID,bookID) values('E_DISCOUNT4','BOOK5');
insert into eventApply(eventID,bookID) values('E_DISCOUNT5','BOOK4'),('E_DISCOUNT5','BOOK5'),('E_DISCOUNT5','BOOK7'),('E_DISCOUNT5','BOOK12'),('E_DISCOUNT5','BOOK13'),('E_DISCOUNT5','BOOK17'),
('E_DISCOUNT5','BOOK6'),('E_DISCOUNT5','BOOK9'),('E_DISCOUNT5','BOOK11'),('E_DISCOUNT5','BOOK19'),('E_DISCOUNT5','BOOK21'),('E_DISCOUNT5','BOOK24');

insert into customerOrder(id,purchaseTime,status,totalCost,totalDiscount,customerID,orderCode) values('ORDER1',SUBTIME(now(),'00:05:00'),true,253.92,115.95,'CUSTOMER1','YIHENYO7SZVT4MNQ'),
('ORDER2',SUBTIME(now(),'00:02:30'),true,19.19,8.79,'CUSTOMER1','RKZM1Y4KS5OKU7OG'),
('ORDER3',null,false,400.9,230.98,'CUSTOMER1',null);

insert into physicalOrder values('ORDER1','211 Ly Thuong Kiet'),('ORDER3','211 Ly Thuong Kiet');
insert into fileOrder values('ORDER1'),('ORDER2'),('ORDER3');

insert into physicalOrderContain(orderID,bookID,amount) values('ORDER1','BOOK4',1),('ORDER1','BOOK5',2),('ORDER1','BOOK6',3),('ORDER1','BOOK7',1),('ORDER1','BOOK21',4);
insert into physicalOrderContain(orderID,bookID,amount) values('ORDER3','BOOK4',10),('ORDER3','BOOK5',2);

insert into fileOrderContain(orderID,bookID) values('ORDER1','BOOK8'),('ORDER1','BOOK1');
insert into fileOrderContain(orderID,bookID) values('ORDER2','BOOK10'),('ORDER2','BOOK21');

insert into discountApply(orderID,discountID) values('ORDER1','E_DISCOUNT1'),('ORDER1','E_DISCOUNT2'),('ORDER1','E_DISCOUNT4'),('ORDER1','R_DISCOUNT1');
insert into discountApply(orderID,discountID) values('ORDER2','E_DISCOUNT1'),('ORDER2','R_DISCOUNT1');
insert into discountApply(orderID,discountID) values('ORDER3','E_DISCOUNT2'),('ORDER3','E_DISCOUNT4'),('ORDER3','R_DISCOUNT1');

insert into customerOrder(id,purchaseTime,status,totalCost,totalDiscount,customerID,orderCode) values('ORDER4',SUBTIME(now(),'00:10:00'),true,259.11,110.76,'CUSTOMER2','B59VDRO7X0QI6NHH'),
('ORDER5',SUBTIME(now(),'00:07:30'),true,19.59,8.39,'CUSTOMER2','0CII886CCA8ZS18O'),
('ORDER6',null,false,409.08,222.8,'CUSTOMER2',NULL);

insert into physicalOrder values('ORDER4','211 Ly Thuong Kiet'),('ORDER6','211 Ly Thuong Kiet');
insert into fileOrder values('ORDER4'),('ORDER5'),('ORDER6');

insert into physicalOrderContain(orderID,bookID,amount) values('ORDER4','BOOK4',1),('ORDER4','BOOK5',2),
('ORDER4','BOOK6',3),('ORDER4','BOOK7',1),('ORDER4','BOOK21',4);
insert into physicalOrderContain(orderID,bookID,amount) values('ORDER6','BOOK4',10),('ORDER6','BOOK5',2);

insert into fileOrderContain(orderID,bookID) values('ORDER4','BOOK8'),('ORDER4','BOOK1');
insert into fileOrderContain(orderID,bookID) values('ORDER5','BOOK10'),('ORDER5','BOOK21');

insert into discountApply(orderID,discountID) values('ORDER4','E_DISCOUNT1'),('ORDER4','E_DISCOUNT2'),('ORDER4','E_DISCOUNT4');
insert into discountApply(orderID,discountID) values('ORDER5','E_DISCOUNT1');
insert into discountApply(orderID,discountID) values('ORDER6','E_DISCOUNT2'),('ORDER6','E_DISCOUNT4');

insert into customerOrder(id,purchaseTime,status,totalCost,totalDiscount,customerID,orderCode) values('ORDER7',SUBTIME(now(),'00:15:00'),true,259.11,110.76,'CUSTOMER3','N7RJ593EYRVHKPED'),
('ORDER8',SUBTIME(now(),'00:01:30'),true,19.59,8.39,'CUSTOMER3','Y6ATT0B5BV3B7R8O'),
('ORDER9',null,false,409.08,222.8,'CUSTOMER3',null);

insert into physicalOrder values('ORDER7','211 Ly Thuong Kiet'),('ORDER9','211 Ly Thuong Kiet');
insert into fileOrder values('ORDER7'),('ORDER8'),('ORDER9');

insert into physicalOrderContain(orderID,bookID,amount) values('ORDER7','BOOK4',1),('ORDER7','BOOK5',2),
('ORDER7','BOOK6',3),('ORDER7','BOOK7',1),('ORDER7','BOOK21',4);
insert into physicalOrderContain(orderID,bookID,amount) values('ORDER9','BOOK4',10),('ORDER9','BOOK5',2);

insert into fileOrderContain(orderID,bookID) values('ORDER7','BOOK8'),('ORDER7','BOOK1');
insert into fileOrderContain(orderID,bookID) values('ORDER8','BOOK10'),('ORDER8','BOOK21');

insert into discountApply(orderID,discountID) values('ORDER7','E_DISCOUNT1'),('ORDER7','E_DISCOUNT2'),('ORDER7','E_DISCOUNT4');
insert into discountApply(orderID,discountID) values('ORDER8','E_DISCOUNT1');
insert into discountApply(orderID,discountID) values('ORDER9','E_DISCOUNT2'),('ORDER9','E_DISCOUNT4');