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
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK27', 'Money Magic', '4', '9780312357891', '13', '4.5', 'Little, Brown and Company', 'https://www.littlebrown.com/', '2007-03-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK28', 'Code Red', '1', '9781416590217', '14', '4.7', 'HarperCollins Publishers', 'https://www.harpercollins.com/', '2009-07-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK29', 'Whisper in the Wilds', '3', '9780765384107', '10', '4.4', 'Macmillan Children`s Publishing Group', 'https://us.macmillan.com/', '2004-05-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK30', 'The Girl with the Timekeeper`s Heart', '4', '9781442347092', '13', '4.6', 'Simon & Schuster Children`s', 'https://www.simonandschuster.com/', '2006-11-01', '1');
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('BOOK31', 'Lord of Goblins, Vol. 2', '3', '9780765384297', '13', '4.5', 'MoonQuill', 'https://www.wikipedia.org/', '2023-04-30', '1');

-- Insert `author` table
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK1', '1', 'Alan Forbes', 'https://www.amazon.com/stores/Alan-Forbes/author/B00BBPOUOA?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK2', '1', 'Mark Manson', 'https://www.amazon.com/stores/Mark-Manson/author/B00BIJOMOC?pd_rd_w=gDj3A&content-id=amzn1.sym.a36c3969-f821-4d5b-a8e8-be129cf4aa4a:amzn1.sym.a36c3969-f821-4d5b-a8e8-be129cf4aa4a&pf_rd_p=a36c3969-f821-4d5b-a8e8-be129cf4aa4a&pf_rd_r=P3DX4X44GWKJKTMDDC5V&pd_rd_wg=AkX2N&pd_rd_r=8bb4709a-dace-44fd-a155-559d1fc185ee&qid=1705117035&cv_ct_cx=mark+manson&ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK3', '1', 'Michiel Werbrouck', 'https://www.amazon.com/stores/Michiel-Werbrouck/author/B089GQ8TC2?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK4', '1', 'Douglas Adams', 'https://www.amazon.com/stores/Douglas-Adams/author/B000AQ2A84?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK5', '1', 'Herbert George Wells', 'https://en.wikipedia.org/wiki/H._G._Wells');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK6', '1', 'Jane Austen', 'https://en.wikipedia.org/wiki/Jane_Austen');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK7', '1', 'Harper Lee', 'https://en.wikipedia.org/wiki/Harper_Lee');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK8', '1', 'Paulo Coelho', 'https://en.wikipedia.org/wiki/Paulo_Coelho');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK9', '1', 'Margaret Atwood', 'https://en.wikipedia.org/wiki/Margaret_Atwood');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK10', '1', 'Yuval Noah Harari', 'https://en.wikipedia.org/wiki/Yuval_Noah_Harari');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK11', '1', 'Bram Stoker', 'https://en.wikipedia.org/wiki/Bram_Stoker');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK12', '1', 'Gabriel García Márquez', 'https://en.wikipedia.org/wiki/Gabriel_Garc%C3%ADa_M%C3%A1rquez');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK13', '1', 'Andy Weir', 'https://en.wikipedia.org/wiki/Andy_Weir');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK14', '1', 'John Green', 'https://en.wikipedia.org/wiki/John_Green');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK15', '1', 'Chinua Achebe', 'https://en.wikipedia.org/wiki/Chinua_Achebe');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK16', '1', 'Agatha Christie', 'https://en.wikipedia.org/wiki/Agatha_Christie');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK17', '1', 'Frank Herbert', 'https://en.wikipedia.org/wiki/Frank_Herbert');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK18', '1', 'John Ronald Reuel Tolkien', 'https://en.wikipedia.org/wiki/J._R._R._Tolkien');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK19', '1', 'Margaret Atwood', 'https://en.wikipedia.org/wiki/Margaret_Atwood');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK20', '1', 'Yuval Noah Harari', 'https://en.wikipedia.org/wiki/Yuval_Noah_Harari');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK21', '1', 'Alexandre Dumas', 'https://en.wikipedia.org/wiki/Alexandre_Dumas');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK22', '1', 'Mary Shelley', 'https://en.wikipedia.org/wiki/Mary_Shelley');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK23', '1', 'Douglas Adams', 'https://www.amazon.com/stores/Douglas-Adams/author/B000AQ2A84?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK24', '1', 'George Orwell', 'https://en.wikipedia.org/wiki/George_Orwell');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK25', '1', 'Arundhati Roy', 'https://en.wikipedia.org/wiki/Arundhati_Roy');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK26', '1', 'Gaia Sol', 'https://www.goodreads.com/author/show/17085302.Gaia_Sol');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK27', '1', 'Laurence Kotlikoff', 'https://www.hachettebookgroup.com/contributor/laurence-kotlikoff/?lens=little-brown-and-company');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK28', '1', 'Kyle Mills', 'https://www.amazon.com/stores/Kyle-Mills/author/B000APXUH0?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK29', '1', 'Adam Shoalts', 'https://www.goodreads.com/author/show/6523990.Adam_Shoalts');
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK30', '1', 'Mitch Albom', 'https://en.wikipedia.org/wiki/Mitch_Albom');

INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('BOOK31', '1', 'Michiel Werbrouck', 'https://www.amazon.com/stores/Michiel-Werbrouck/author/B089GQ8TC2?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');

-- Insert `category`
INSERT INTO `bookstore`.`category` (`name`) VALUES
  ('Fiction'),
  ('Fantasy'),
  ('Mystery'),
  ('Thriller'),
  ('Romance'),
  ('Historical fiction'),
  ('Science fiction'),
  ('Nonfiction'),
  ('Biography'),
  ('Memoir'),
  ('History'),
  ('Self-help'),
  ('Business'),
  ('Children`s books'),
  ('Tutorial');

-- Insert `belong1`
  INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK1', 'Tutorial');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK1', 'Nonfiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK2', 'Nonfiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK2', 'Self-help');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK2', 'Romance');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK3', 'Fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK3', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK4', 'Science fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK4', 'Thriller');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK5', 'Science fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK5', 'Children`s books');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK6', 'Historical fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK7', 'Science fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK8', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK9', 'Historical fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK10', 'History');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK11', 'Historical fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK12', 'Science fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK13', 'Science fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK14', 'Nonfiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK15', 'Mystery');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK16', 'Fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK17', 'Science fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK18', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK19', 'Historical fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK20', 'History');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK21', 'Historical fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK22', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK23', 'Fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK24', 'Historical fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK25', 'Mystery');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK26', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK27', 'Biography');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK28', 'Memoir');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK29', 'Fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK30', 'Romance');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK31', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK11', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK13', 'Thriller');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK14', 'Fantasy');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK17', 'Historical fiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK17', 'Mystery');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK25', 'Thriller');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK27', 'Business');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK27', 'Nonfiction');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK28', 'Biography');
INSERT INTO `bookstore`.`belong` (`bookID`, `category`) VALUES ('BOOK28', 'Nonfiction');
