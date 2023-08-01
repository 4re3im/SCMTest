USE `cap`;

INSERT INTO cap.countries (name, iso_code) VALUES ('Australia', 'AU'), ('New Zealand', 'NZ'), ('All', 'ALL');

INSERT INTO cap.states (country_id, name)
VALUES
(1, 'Australian Capital Territory'),
(1, 'New South Wales'),
(1, 'Northern Territory'),
(1, 'South Australia'),
(1, 'Queensland'),
(1, 'Tasmania'),
(1, 'Victoria'),
(1, 'Western Australia'),
(2, 'Northland'),
(2, 'Auckland'),
(2, 'Waikato'),
(2, 'Bay of Plenty'),
(2, 'Gisborne'),
(2, 'Hawke\'s Bay'),
(2, 'Taranaki'),
(2, 'Manawatu-Wanganui'),
(2, 'Wellington'),
(2, 'Tasman'),
(2, 'Nelson'),
(2, 'Marlborough'),
(2, 'West Coast'),
(2, 'Canterbury'),
(2, 'Otago'),
(2, 'Southland'),
(3, 'All Regions');


INSERT INTO cap.year_levels (level)
VALUES
('F'),
('F-2'),
('F-6'),
('1'),
('2'),
('3'),
('4'),
('5'),
('6'),
('7'),
('8'),
('9'),
('10'),
('11'),
('12'),
('3-4'),
('5-6'),
('7-8'),
('7-10'),
('9-10'),
('11-12');


INSERT INTO cap.tab_accesses (name)
VALUES
('Free'),
('Login Only'),
('Subscription');


INSERT INTO cap.types (id, name, is_active)
VALUES
(1005, 'File', 1),
(1001, 'Link', 1),
(1006, 'HTML', 1),
(1004, 'Subheading', 1),
(1002, 'List', 1);


INSERT INTO `cap`.`editions` (name) 
VALUES 
('First Edition'),
('Second Edition'),
('Third Edition'),
('Fourth Edition'),
('Fifth Edition'),
('Sixth Edition'),
('Seventh Edition'),
('Eighth Edition'),
('Nineth Edition'),
('Tenth Edition');


INSERT INTO `cap`.`divisions` (name)
VALUES
('Primary'),
('Secondary'),
('Tertiary');


INSERT INTO `cap`.`formats` (id, name, pretty_url, description, is_digital, created_at, updated_at)
SELECT id, name, prettyUrl, longDescription, isDigital, createdAt, modifiedAt
FROM `tngdb`.`CupContentFormat`;


-- insert missing subjects first
INSERT INTO `tngdb`.`CupContentSubject` (name, prettyUrl, isPrimary, isSecondary, description, region, createdAt, modifiedAt) SELECT DISTINCT(subject), 'Unverified-Pretty-Url', 1, 1, 'Description', 'ALL', NOW(), NOW() FROM `tngdb`.`CupContentTitleSubjects` WHERE subject NOT IN (SELECT name FROM `tngdb`.`CupContentSubject`);
INSERT INTO `cap`.`subjects` (id, country_id, name, pretty_url, is_primary, is_secondary, description, created_at, updated_at)
SELECT id, CASE WHEN region = 'AU' THEN 1 ELSE 3 END, name, prettyUrl, isPrimary, isSecondary, description, createdAt, modifiedAt
FROM `tngdb`.`CupContentSubject`;


-- insert missing authors first
INSERT INTO `tngdb`.`CupContentAuthor` (name, prettyUrl, biography, createdAt, modifiedAt) SELECT DISTINCT(author), 'Unverified-Pretty-Url', 'Unknown Biography', NOW(), NOW() FROM `tngdb`.`CupContentTitleAuthors` WHERE author NOT IN (SELECT name FROM `tngdb`.`CupContentAuthor`);

INSERT INTO `cap`.`authors` (id, name, pretty_url, biography, created_at, updated_at)
SELECT id, name, prettyUrl, biography, createdAt, modifiedAt
FROM `tngdb`.`CupContentAuthor`;