#Create Database

create table Patient (
  number integer,
  name varchar(255),
  address varchar(255),
  primary key(number));

create table PAN (
  domain varchar(255),
  phone integer,
  primary key(domain));

create table Device (
  serialnum integer,
  manufacturer varchar(255),
  description varchar(255),
  primary key(snum,manuf));


create table Sensor (
  snum integer,
  manuf varchar(255),
  units varchar(255),
  primary key(snum,manuf),
  foreign key(snum,manuf) references Device(snum,manuf));

create table Actuator (
  snum integer,
  manuf varchar(255),
  units varchar(255),
  primary key(snum,manuf),
  foreign key(snum,manuf) references Device(snum,manuf));

create table Municipality (
  nut4code varchar(255),
  name varchar(255),
  primary key(nut4code));

create table Period (
  start timestamp,
  end timestamp,
  primary key(start,end));

create table Reading (
  snum integer,
  manuf varchar(255),
  datetime timestamp,
  value   numeric(20,2),
  primary key(snum,manuf,datetime),
  foreign key(snum,manuf) references Sensor (snum,manuf));

create table Setting (
  snum integer,
  manuf varchar(255),
  datetime timestamp,
  value numeric(20,2),
  primary key(snum,manuf,datetime),
  foreign key(snum,manuf) references Actuator (snum,manuf));

create table Wears (
  start timestamp,
  end timestamp,
  patient integer,
  pan varchar(255),
  primary key(start,end,patient),
  foreign key(start,end) references Period (start,end),
  foreign key(patient) references Patient (number),
  foreign key(pan) references PAN (domain));

create table Lives (
  start timestamp,
  end timestamp,
  patient integer,
  muni varchar(255),
  primary key(start,end,patient),
  foreign key(start,end) references Period (start,end),
  foreign key(patient) references Patient (number),
  foreign key(muni) references Municipality (nut4code));

create table Connects (
  start timestamp,
  end timestamp,
  snum integer,
  manuf varchar(255),
  pan varchar(255),
  primary key(start,end,snum,manuf),
  foreign key(start,end) references Period (start,end),
  foreign key(snum,manuf) references Device (snum,manuf),
  foreign key(pan) references PAN (domain));
