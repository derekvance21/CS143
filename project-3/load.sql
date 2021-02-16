DROP TABLE IF EXISTS Wins;
DROP TABLE IF EXISTS Affiliations;
DROP TABLE IF EXISTS Laureate;
DROP TABLE IF EXISTS Prize;

CREATE TABLE Laureate (
    id INT,
    givenName VARCHAR(30),
    familyName VARCHAR(30),
    orgName VARCHAR(60),
    gender VARCHAR(10),
    originDate DATE,
    originCity VARCHAR(40),
    originCountry VARCHAR(60),
    PRIMARY KEY(id)
);

CREATE TABLE Prize (
    year YEAR,
    category VARCHAR(30),
    dateAwarded DATE,
    prizeAmount INT,
    PRIMARY KEY(year, category)
);

CREATE TABLE Wins (
    lid INT,
    year YEAR,
    category VARCHAR(30),
    portion VARCHAR(5),
    sortOrder INT,
    motivation TEXT(400),
    prizeStatus VARCHAR(30),
    FOREIGN KEY (lid) REFERENCES Laureate(id),
    FOREIGN KEY (year, category) REFERENCES Prize(year, category)
);

CREATE TABLE Affiliations (
    lid INT,
    year YEAR,
    category VARCHAR(30),
    name VARCHAR(60),
    city VARCHAR(40),
    country VARCHAR(60),
    FOREIGN KEY (lid) REFERENCES Laureate(id),
    FOREIGN KEY (year, category) REFERENCES Prize(year, category)
);

LOAD DATA LOCAL INFILE "./laureates.del" INTO TABLE Laureate 
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"';

LOAD DATA LOCAL INFILE "./prizes.del" INTO TABLE Prize 
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"';

LOAD DATA LOCAL INFILE "./win.del" INTO TABLE Wins 
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"';

LOAD DATA LOCAL INFILE "./affiliated.del" INTO TABLE Affiliations 
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"';
