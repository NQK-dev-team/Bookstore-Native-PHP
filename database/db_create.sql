drop schema if exists bookstore;

create schema bookstore;

use bookstore;

-- Important --
create table pointConfig(
	pointConversionRate double primary key
);
insert into pointConfig values(5);
-- Important --

create table category(
    id varchar(20) primary key,
	name varchar(255) not null unique,
    description text
);

create table appUser(
	id varchar(20) primary key,
    name varchar(255) not null,
    dob date not null,
    address text,
    phone varchar(10) unique,
    email varchar(255) unique,
    password varchar(255) not null,
    check(length(password)>=8),
    imagePath text
);

create table admin(
	id varchar(20) primary key references appUser(id) on delete cascade on update cascade
);

create table customer(
	id varchar(20) primary key references appUser(id) on delete cascade on update cascade,
    cardNumber varchar(16),
    point double default 0,
    check(point>=0),
    referrer varchar(20) references customer(id) on delete set null on update cascade,
    status boolean not null default true
);

create table book(
	id varchar(20) primary key,
    name varchar(255) not null,
    edition int not null,
    unique(name,edition),
    isbn varchar(13) not null unique,
    ageRestriction int,
    check(ageRestriction>0),
    avgRating double,
    check(avgRating>=0 and avgRating<=5),
    publisher varchar(255) not null,
    publishDate date not null,
    status boolean not null default true, -- true means the book is purchasable, false means not
    imagePath text,
    description text
);

create table author(
	bookID varchar(20),
    authorIdx int, -- This only used to combine with `bookID` to form a primary key, no further usage other than that.
    check(authorIdx>=1),
    authorName varchar(255) not null,
    primary key(bookID,authorIdx)
);

create table belong(
	bookID varchar(20) references book(id) on delete cascade on update cascade,
    categoryID varchar(20) references category(id) on delete cascade on update cascade,
    primary key(bookID,categoryID)
);

create table fileCopy(
	id varchar(20) primary key references book(id) on delete cascade on update cascade,
    price double,
    check (price>0),
    filePath text
);

create table physicalCopy(
	id varchar(20) primary key references book(id) on delete cascade on update cascade,
    price double,
    check (price>0),
	inStock int,
    check(inStock>=0)
);

create table rating(
	customerID varchar(20) references customer(id) on delete cascade on update cascade,
    bookID varchar(20) references book(id) on delete cascade on update cascade,
    primary key(customerID,bookID),
    star double not null,
    check(star>=0 and star<=5)
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
    commentIdx int default 1, -- This only used to form a primary key, no further usage other than that.
	check(commentIdx>=1),
    primary key(customerID,bookID,commentIdx),
    commentTime datetime not null,
    content text not null
);

create table customerOrder(
	id varchar(20) primary key,
    time datetime,
    status boolean not null default false, -- true means the order has been purchased, false means not
    totalCost double not null, -- cost after using discount coupons
    check(totalCost>=0),
    totalDiscount double not null,
    check(totalDiscount>=0),
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
	amount int not null default 1,
    check(amount>=1),
    destinationAddress text not null
);

create table discount(
	id varchar(20) primary key,
    name varchar(255) not null,
    status boolean not null default true -- true means the discount is appliable, false means not
);

create table discountApply(
	orderID varchar(20) references customerOrder(id) on delete cascade on update cascade,
    discountID varchar(20) references discount(id) on delete cascade on update cascade,
    primary key(orderID,discountID)
);

create table customerDiscount(
	id varchar(20) primary key references discount(id) on delete cascade on update cascade,
    point double not null,
    check(point>0),
    discount double not null,
    check(0<discount and discount<=100),
	unique(id,point)
);

create table referrerDiscount(
	id varchar(20) primary key references discount(id) on delete cascade on update cascade,
    numberOfPeople int not null,
    check(numberOfPeople>=1),
    discount double not null,
    check(0<discount and discount<=100),
    unique(id,numberOfPeople)
);

create table eventDiscount(
	id varchar(20) primary key references discount(id) on delete cascade on update cascade,
    discount double not null,
    check(discount>0 and discount<=100),
    startDate date not null,
    endDate date not null,
    check(startDate<=endDate),
    applyForAll boolean default false -- true means all the books are discounted by applying this coupon, false means only a number of books are discounted
);

create table eventApply(
	eventID varchar(20) references eventDiscount(id) on delete cascade on update cascade,
    bookID varchar(20) references book(id) on delete cascade on update cascade,
    primary key(eventID,bookID)
); -- Used to tell which books are discounted by the discount event that has `applyForAll` set to false