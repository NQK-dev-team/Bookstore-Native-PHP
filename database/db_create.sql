drop schema if exists bookstore;

create schema bookstore;

use bookstore;
                              
-- Important --
SET @nullVar = NULL;

create table pointConfig(
	locker char(1) primary key,
	pointConversionRate double not null,
    check (locker='X'),
    check (pointConversionRate>0)
);
insert into pointConfig values('X',10);
-- Important --

create table category(
    id varchar(20) primary key,
	name varchar(255) not null unique,
    description varchar(500)
);

create table appUser(
	id varchar(20) primary key,
    name varchar(255) not null,
    dob date not null,
    address varchar(1000),
    phone varchar(10) unique,
    email varchar(255) unique,
    password varchar(255),
    check(length(password)>=8),
    imagePath varchar(1000),
    gender varchar(1) not null,
    check (gender='F' or gender='M' or gender='O')
);

create table admin(
	id varchar(20) primary key,
    foreign key (id) references appUser(id) on delete cascade on update cascade
);

create table customer(
	id varchar(20) primary key,
    point double default 0 not null,
    check(point>=0),
    referrer varchar(20),
    status boolean not null default true,
    deleteTime datetime, -- when current datetime>=delete time, set email and phone to null
    foreign key (id) references appUser(id) on delete cascade on update cascade,
    foreign key (referrer) references customer(id) on delete set null on update cascade
);

create table book(
	id varchar(20) primary key,
    name varchar(255) not null,
    edition int not null,
    unique(name,edition),
    isbn varchar(13) not null unique,
    avgRating double not null default 0,
    check(avgRating>=0 and avgRating<=5),
    publisher varchar(255) not null,
    publishDate date not null,
    status boolean not null default true, -- true means the book is purchasable, false means not
    imagePath varchar(1000) not null,
    description varchar(2000)
);

create table author(
	bookID varchar(20),
    authorIdx int, -- This only used to combine with `bookID` to form a primary key, no further usage other than that.
    check(authorIdx>=1),
    authorName varchar(255) not null,
    primary key(bookID,authorIdx),
    foreign key (bookID) references book(id) on delete cascade on update cascade
);

create table belong(
	bookID varchar(20),
    categoryID varchar(20),
    primary key(bookID,categoryID),
    foreign key (bookID) references book(id) on delete cascade on update cascade,
    foreign key (categoryID) references category(id) on delete cascade on update cascade
);

create table fileCopy(
	id varchar(20) primary key,
    price double,
    check (price>0),
    filePath varchar(1000),
    foreign key (id) references book(id) on delete cascade on update cascade
);

create table physicalCopy(
	id varchar(20) primary key,
    price double,
    check (price>0),
	inStock int not null default 0,
    check(inStock>=0),
    foreign key (id) references book(id) on delete cascade on update cascade
);

create table rating(
	customerID varchar(20),
    bookID varchar(20),
    primary key(customerID,bookID),
    star int not null,
    check(star>=0 and star<=5),
    comment text,
    ratingTime datetime not null default CURRENT_TIMESTAMP,
    foreign key (customerID) references customer(id) on delete cascade on update cascade,
    foreign key (bookID) references book(id) on delete cascade on update cascade
);

create table customerOrder(
	id varchar(20) primary key,
    purchaseTime datetime,
    status boolean not null default false, -- true means the order has been purchased, false means not
    totalCost double not null default 0, -- cost after using discount coupons
    check(totalCost>=0),
    totalDiscount double not null default 0,
    check(totalDiscount>=0),
    customerID varchar(20) not null,
    orderCode varchar(16) unique,
    foreign key (customerID) references customer(id) on delete cascade on update cascade,
    check((status and purchaseTime is not null) or (!status and purchaseTime is null)),
    check((status and orderCode is not null) or (!status and orderCode is null))
);

create table physicalOrder(
	id varchar(20) primary key,
    foreign key (id) references customerOrder(id) on delete cascade on update cascade,
	destinationAddress varchar(1000) not null
);

create table fileOrder(
	id varchar(20) primary key,
    foreign key (id) references customerOrder(id) on delete cascade on update cascade
);

create table fileOrderContain(
    bookID varchar(20),
    orderID varchar(20),
    primary key(bookID,orderID),
    foreign key (bookID) references fileCopy(id) on delete cascade on update cascade,
    foreign key (orderID) references fileOrder(id) on delete cascade on update cascade
);

create table physicalOrderContain(
    bookID varchar(20),
    orderID varchar(20),
    primary key(bookID,orderID),
	amount int not null default 1,
    check(amount>=1),
    foreign key (bookID) references physicalCopy(id) on delete cascade on update cascade,
    foreign key (orderID) references physicalOrder(id) on delete cascade on update cascade
);

create table discount(
	id varchar(20) primary key,
    name varchar(255) not null,
    status boolean not null default true -- true means the discount is appliable, false means not
);

create table discountApply(
	orderID varchar(20),
    discountID varchar(20),
    primary key(orderID,discountID),
    foreign key (orderID) references customerOrder(id) on delete cascade on update cascade,
    foreign key (discountID) references discount(id) on delete cascade on update cascade
);

create table customerDiscount(
	id varchar(20) primary key,
    point double not null,
    check(point>0),
    discount double not null,
    check(0<discount and discount<=100),
    foreign key (id) references discount(id) on delete cascade on update cascade
);

create table referrerDiscount(
	id varchar(20) primary key,
    numberOfPeople int not null,
    check(numberOfPeople>=1),
    discount double not null,
    check(0<discount and discount<=100),
    foreign key (id) references discount(id) on delete cascade on update cascade
);

create table eventDiscount(
	id varchar(20) primary key,
    discount double not null,
    check(discount>0 and discount<=100),
    startDate date not null,
    endDate date not null,
    check(startDate<=endDate),
    applyForAll boolean default false, -- true means all the books are discounted by applying this coupon, false means only a number of books are discounted
    foreign key (id) references discount(id) on delete cascade on update cascade,
    isNotify boolean not null default false -- use for cron job to know whether or not it should send an email to notify all the customers
);

create table eventApply(
	eventID varchar(20),
    bookID varchar(20),
    primary key(eventID,bookID),
    foreign key (eventID) references eventDiscount(id) on delete cascade on update cascade,
    foreign key (bookID) references book(id) on delete cascade on update cascade
); -- Used to tell which books are discounted by the discount event that has `applyForAll` set to false

create table request(
	id INT AUTO_INCREMENT PRIMARY KEY,
    name varchar(255) not null,
    author varchar(1000) not null,
    requestTime datetime not null default now()
);