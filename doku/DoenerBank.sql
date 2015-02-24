CREATE TABLE doener_artikel (
	art_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, 
	name VARCHAR(255) NOT NULL,
	preis DOUBLE NOT NULL,
	kategorie VARCHAR(255) NOT NULL,
	beschreibung VARCHAR(255) DEFAULT NULL
);
CREATE TABLE doener_nutzer (
	user_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	passwort VARCHAR(255),
	admin ENUM("0","1") DEFAULT"0"
);
CREATE TABLE doener_tagesestellung(
	best_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	datum VARCHAR(255) NOT NULL,
	gesamtpreis DOUBLE NOT NULL,
	bemerkungen TEXT DEFAULT NULL
);

CREATE TABLE doener_artikelliste (
	artlist_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	ebest_id INT NOT NULL,
	art_id INT NOT NULL
);
	
CREATE TABLE doener_einzelbestellung (
	ebest_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	user_id INT NOT NULL,
	best_id INT NOT NULL
);

ALTER TABLE  `doener_nutzer` ADD  `loggedIn` VARCHAR( 255 ) NULL AFTER  `user_id` ;
/**
CREATE TABLE Artikel (
	art_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, 
	name VARCHAR(255) NOT NULL,
	preis DOUBLE NOT NULL,
	kategorie VARCHAR(255) NOT NULL,
	beschreibung VARCHAR(255) DEFAULT NULL
);
CREATE TABLE Nutzer (
	user_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	name VARCHAR(255),
	passwort VARCHAR(255),
	admin ENUM("0","1") DEFAULT("0")
);
CREATE TABLE Tagesestellung(
	best_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	datum VARCHAR(255) NOT NULL,
	gesamtpreis DOUBLE NOT NULL,
	bemerkungen TEXT DEFAULT NULL
);

CREATE TABLE Artikelliste (
	artlist_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	ebest_id INT NOT NULL,
	art_id INT NOT NULL
);
	
CREATE TABLE einzelbestellung (
	ebest_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	user_id INT NOT NULL,
	best_id INT NOT NULL
);
**/