USE simple_analytics;

SELECT "Creating tables" as "Doing";

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(32) NOT NULL
)Engine=InnoDB;

DROP TABLE IF EXISTS articles;
CREATE TABLE articles (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(128) NOT NULL
)Engine=InnoDB;

DROP TABLE IF EXISTS attributes;
CREATE TABLE attributes (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(32) NOT NULL
)Engine=InnoDB;

DROP TABLE IF EXISTS article_attributes;
CREATE TABLE article_attributes (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  article_id INT UNSIGNED NOT NULL,
  article_attribute_id INT UNSIGNED NOT NULL
)Engine=InnoDB;

DROP TABLE IF EXISTS impressions;
CREATE TABLE impressions (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  article_id INT UNSIGNED NOT NULL,
  date DATETIME NOT NULL
)Engine=InnoDB;


SELECT "Populating some fake data" as "Doing";

INSERT INTO users (username) values 
  ("Sean Abbi"),
  ("Arsenio Porsche"),
  ("Abigail Linnie"),
  ("Wilton Irvine"),
  ("Kayleen Albine");
  
INSERT INTO attributes (title) values
  ("Sports"),   # 1
  ("Politics"), # 2
  ("Local"),    # 3
  ("Entertainment"), #4
  ("Family"),   # 5
  ("Religion"), # 6
  ("Other");    # 7

INSERT INTO articles (title) values
  ("Tales from the ER: Nurses see lots of tragedies, deaths (P)"), # 1
  ("The anti-technologist: How to have a smartphone without a data plan (E)"),
  ("Prodigal Dad's wife wants a gun (F, R)"), # 3
  ("Bill allows concealed weapons to be carried without permit (P)"), # 4
  ("Why 'Jack the Giant Slayer' is and isn't a family film (E, F)"), # 5
  ("Space station capsule engine problem may be fixed (O)"), # 6
  ("International baseball begins; landscape swings; and missed dunks (S)"), # 7
  ("300 East, public safety building closed for 2 months due to improvements (O)"), # 8
  ("Draper Cax (L, O)"), # 9
  ("Newborn owls born on webcam (E, F)"), # 10
  ("Obama says he can't 'Jedi mind meld' a budget deal (P)"), # 11
  ("2 arrested in 6-month Provo crime spree (L, P)"), # 12
  ("BYU Football: Howell named DC; Bronco to call plays (L, S)"); # 13
  
INSERT INTO article_attributes (article_id, article_attribute_id) values
  (1, 2),
  (2, 4),
  (3, 5),
  (3, 6),
  (4, 2),
  (5, 4),
  (5, 5),
  (6, 7),
  (7, 1),
  (8, 7),
  (9, 7),
  (9, 3),
  (10, 4),
  (10, 5),
  (11, 2),
  (12, 2),
  (12, 3),
  (13, 1),
  (13, 3);
