CREATE TABLE doener_artikel (
	art_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, 
	`name` VARCHAR(255) NOT NULL,
	preis DOUBLE NOT NULL,
	kategorie VARCHAR(255) NOT NULL,
	beschreibung VARCHAR(255) DEFAULT NULL
);
CREATE TABLE doener_nutzer (
	user_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `loggedIn` VARCHAR( 255 ) NULL,
	`name` VARCHAR(255),
    `email` VARCHAR( 255 ) NULL,
	passwort VARCHAR(255),
	`admin` ENUM("0","1") DEFAULT"0"
);
CREATE TABLE doener_tagesbestellung(
	best_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	datum VARCHAR(255) NOT NULL,
	gesamtpreis DOUBLE NOT NULL,
    closed ENUM("0","1") DEFAULT"0"
);

CREATE TABLE doener_artikelliste (
	artlist_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	ebest_id INT NOT NULL,
	art_id INT NOT NULL,
	bemerkungen TEXT DEFAULT NULL
);
	
CREATE TABLE doener_einzelbestellung (
	ebest_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	user_id INT NOT NULL,
	best_id INT NOT NULL,
    ebest_preis DOUBLE NOT NULL,
    bestaetigt ENUM("0","1") DEFAULT"0"
);

CREATE VIEW bestellung_gesamt AS SELECT a.name as Artikelname,a.preis as Artikelpreis,
        n.name Nutzer, tb.datum as datum, eb.ebest_preis as NutzerGesamtPreis, eb.ebest_id as EinzelbestellungID 
        FROM doener_tagesbestellung tb
        JOIN doener_einzelbestellung eb ON tb.best_id = eb.best_id
        JOIN doener_nutzer n ON eb.user_id = n.user_id
        JOIN doener_artikelliste al ON eb.ebest_id = al.ebest_id
        JOIN doener_artikel a ON al.art_id = a.art_id
        ORDER BY eb.ebest_id;

CREATE VIEW user_bestellung AS SELECT 
        al.ebest_id,
        art.name, 
        art.preis, 
        art.kategorie, 
        art.beschreibung
        FROM doener_artikelliste al 
        JOIN doener_artikel art 
        ON al.art_id = art.art_id;
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