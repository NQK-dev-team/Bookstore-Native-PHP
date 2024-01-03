drop schema if exists bookstore;

create schema bookstore;

use bookstore;

-- Important
create table pointConfig(
	pointConversionRate double primary key
);
insert into pointConfig values(5);

create table category(
	name varchar(100) primary key
);

create table user(
	id varchar(20) primary key,
    name varchar(100) not null,
    dob date not null,
    address text,
    phone varchar(10) unique,
    email varchar(100) unique,
    password varchar(20) not null check(length(password)>=8)
);
INSERT INTO `bookstore`.`user` (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('1', 'khoa', '2000-12-12', '211 Ly Thuong Kiet', '0932758467', 'khoa.liang.business@gmail.com', '12345678');
INSERT INTO `bookstore`.`user` (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('2', 'quang', '2002-12-12', '211 Ly Thuong Kiet', '0932758489', 'quang.liang.business@gmail.com', '12345678');
INSERT INTO `bookstore`.`user` (`id`, `name`, `dob`, `address`, `phone`, `email`, `password`) VALUES ('3', 'nghia', '2002-6-12', '211 Ly Thuong Kiet', '0932758421', 'nghia.liang.business@gmail.com', '12345678');

create table admin(
	id varchar(20) primary key references user(id) on delete cascade on update cascade
);

create table customer(
	id varchar(20) primary key references user(id) on delete cascade on update cascade,
    cardNumber varchar(16),
    point double default 0 check(point>=0),
    referrer varchar(20) references customer(id) on delete set null on update cascade,
    status boolean not null default true
);
INSERT INTO `bookstore`.`admin` (`id`) VALUES ('1');
INSERT INTO `bookstore`.`admin` (`id`) VALUES ('2');

create table book(
	id varchar(20) primary key,
    name varchar(100) not null,
    edition int not null,
    unique(name,edition),
    isbn varchar(13) not null unique,
    ageRestriction int check(ageRestriction>0 and ageRestriction<=30),
    avgRating double check(avgRating>=0 and avgRating<=5),
    publisher varchar(100) not null,
    publisherLink text,
    publishDate date not null,
    status boolean not null default true -- true means the book is purchasable, false means not
);
INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('3', 'Lord of Goblins, Vol. 1 Definitive Edition (Lord of Goblins', '1', ' 979889930075', '3', '4.5', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2023-6-8', '1');

INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('2', 'Models: Attract Women Through Honesty', '3', ' B00C93Q5KK', '13', '4.7', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-30', '1');

INSERT INTO `bookstore`.`book` (`id`, `name`, `edition`, `isbn`, `ageRestriction`, `avgRating`, `publisher`, `publisherLink`, `publishDate`, `status`) VALUES ('1', 'The Joy of PHP', '1', ' 978494267353', '3', '4', 'CreateSpace Independent Publishing Platform', 'https://en.wikipedia.org/wiki/CreateSpace', '2012-12-13', '1');
create table author(
	bookID varchar(20),
    authorIdx int check(authorIdx>=0) default 0, -- This only used to combine with `bookID` to form a primary key, no further usage other than that.
    authorName varchar(100) not null,
    wikiLink text,
    primary key(bookID,authorIdx)
);
INSERT INTO `bookstore`.`author` (`bookID`, `authorIdx`, `authorName`, `wikiLink`) VALUES ('3', '1', 'Michiel Werbrouck', 'https://www.amazon.com/stores/Michiel-Werbrouck/author/B089GQ8TC2?ref=ap_rdr&isDramIntegrated=true&shoppingPortalEnabled=true');

create table belong(
	bookID varchar(20) references book(id) on delete cascade on update cascade,
    category varchar(100) references category(name) on delete cascade on update cascade,
    primary key(bookID,category)
);

create table fileCopy(
	id varchar(20) primary key references book(id) on delete cascade on update cascade,
    price double not null check (price>=0),
    filePath text
);

create table physicalCopy(
	id varchar(20) primary key references book(id) on delete cascade on update cascade,
    price double not null check (price>=0),
	inStock int check(inStock>=0)
);

create table rating(
	customerID varchar(20) references customer(id) on delete cascade on update cascade,
    bookID varchar(20) references book(id) on delete cascade on update cascade,
    primary key(customerID,bookID),
    star double not null check(star>=0 and star<=5)
);

create table wishlist(
	customerID varchar(20) references customer(id) on delete cascade on update cascade,
    bookID varchar(20) references book(id) on delete cascade on update cascade,
    primary key(customerID,bookID),
    flag boolean default true -- true means physical wishlist (get notification when discounted, restocked or emptied), false means file wishlist (get notification when available or discounted)
);

create table comment(
	customerID varchar(20) references customer(id) on delete cascade on update cascade,
    bookID varchar(20) references book(id) on delete cascade on update cascade,
    primary key(customerID,bookID)
);

create table commentContent(
	customerID varchar(20) references comment(customerID) on delete cascade on update cascade,
    bookID varchar(20) references comment(bookID) on delete cascade on update cascade,
    commentIdx int default 0 check(commentIdx>=0), -- This only used to form a primary key, no further usage other than that.
    primary key(customerID,bookID,commentIdx),
    commentTime datetime not null,
    content text not null
);

create table customerOrder(
	id varchar(20) primary key,
    time datetime,
    status boolean not null default false, -- true means the order has been purchased, false means not
    totalCost double not null check(totalCost>=0), -- cost after using discount coupons
    totalDiscount double not null check(totalDiscount>=0),
    customerID varchar(20) not null references customer(id) on delete cascade on update cascade
);

create table physicalOrder(
	id varchar(20) primary key references customerOrder(id) on delete cascade on update cascade
);

create table fileOrder(
	id varchar(20) primary key references customerOrder(id) on delete cascade on update cascade
);

create table fileOrderContain(
    bookID varchar(20) references fileCopy(id) on delete cascade on update cascade,
    orderID varchar(20) references fileOrder(id) on delete cascade on update cascade,
    primary key(bookID,orderID)
);

create table physicalOrderContain(
    bookID varchar(20) references physicalCopy(id) on delete cascade on update cascade,
    orderID varchar(20) references physicalOrder(id) on delete cascade on update cascade,
    primary key(bookID,orderID),
	amount int not null default 1 check(amount>=1),
    destinationAddress text not null
);

create table discount(
	id varchar(20) primary key,
    status boolean not null default true -- true means the discount is appliable, false means not
);

create table discountApply(
	orderID varchar(20) references customerOrder(id) on delete cascade on update cascade,
    discountID varchar(20) references discount(id) on delete cascade on update cascade,
    primary key(orderID,discountID)
);

create table customerDiscount(
	id varchar(20) primary key references discount(id) on delete cascade on update cascade,
    point double not null check(point>0),
    discount double not null check(0<discount and discount<=100),
	unique(id,point)
);

create table referrerDiscount(
	id varchar(20) primary key references discount(id) on delete cascade on update cascade,
    numberOfPeople int not null check(numberOfPeople>=1),
    discount double not null check(0<discount and discount<=100),
    unique(id,numberOfPeople)
);

create table eventDiscount(
	id varchar(20) primary key references discount(id) on delete cascade on update cascade,
    discount double not null check(discount>0 and discount<=100),
    startDate date not null,
    endDate date not null,
    applyForAll boolean default false -- true means all the books are discounted by applying this coupon, false means only a number of books are discounted
);

create table eventApply(
	eventID varchar(20) references eventDiscount(id) on delete cascade on update cascade,
    bookID varchar(20) references book(id) on delete cascade on update cascade,
    primary key(eventID,bookID)
); -- Used to tell which books are discounted by the discount event that has `applyForAll` set to false