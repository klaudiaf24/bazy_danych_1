SET SEARCH_PATH TO Kartoteka;

INSERT INTO Prokurator (imie, nazwisko, email, haslo, miejsce_pracy, numer_licencji) VALUES 
('Anna Maria','Wesołowska',	'annamariawesolowska@wp.pl',	'annamaria123',		'Warszawa',	98062),
('Artur',	'Łata',		'artur.lata90@gmail.com',	'arturPROKURATOR',	'Kraków',	97050),
('Paweł',	'Sobczak',	'pawel_sobczak@wp.pl',		'haslo7817',		'Rzeszów',	65650);

INSERT INTO Adwokat (imie, nazwisko, email, haslo, nazwa_kancelarii, numer_licencji) VALUES 
('Magdalena', 	'Wilk', 	'magdawilk@onet.pl', 		'wilczeK88', 		'Wilk&Lasko',73227),
('Andrzej', 	'Mękal', 	'andrzej_mekal@o2.pl', 		'andrzej2018', 		'brak',		99042),
('Magdalena',	'Grześkowiak',	'grzeskowiak_AnD@gmail.com',	'admin123#',		'Artur&Donata',	10872),
('Rafał',	'Zawalski',	'r.zawalski7@onet.pl',		'cristianoRonaldo7',	'Zawoo',	99283),
('Barbara',	'Lasko',	'basia.lasko9391@o2.pl',	'czarnySmok',		'Wilk&Lasko',29875);

INSERT INTO Oskarzony (imie, nazwisko, email, haslo, adwokat_id, prokurator_id) VALUES 
('Krystian', 	'Lampa', 	'krystian.lampa17@wp.pl', 		    'sezamki666',       1,      1),
('Justyna', 	'Podłoga', 	'justyna_podloga_snk@gmail.com', 	'justysia@!',       1,      2),
('Roman',    	'Kringe',	'roman.kringe@gmai.com',	    	'romanZdomu',       1,      3),
('Mira',    	'Bojan',	'bojan.mira@o2.pl',		        'mirusia_bojan2',       2,      1),
('Katarzyna',	'Kalemb',	'katarzyna_kalemb6580@wp.pl',		'herbata_z_miodem', 2,      2);

INSERT INTO Wiezienie (id, kraj, miasto, typ) VALUES (0, 'brak', 'brak', 'brak');

INSERT INTO Wiezienie (kraj, miasto, typ) VALUES
('Polska', 	'Kraków', 	    'zamknięte'),
('Chiny', 	'Hongkong', 	'półotwarte'),
('Polska', 	'Kraków', 	    'otwarte'),
('USA',	 	'Fox River', 	'zamknięte'),
('USA',	 	'Zielona Mila',	'zamknięte'),
('USA',  	'Shawshank', 	'półotwarte'),
('Polska', 	'Stary Łupków',	'otwarte'),
('Wielka Brytania', 'Azkaban', 	'zamknięte'),
('USA',	 	'Alkatraz', 	'zamknięte'),
('Francja', 'Bastylia', 	'półotwarte');


INSERT INTO Miejsce_przestepstwa (kraj, miasto) VALUES
('Polska',	        'Gdańsk'),
('Polska',	        'Rzeszów'),
('Wielka Brytania',	'London'),
('Hiszpania',	    'Barcelona'),
('Niemcy',	        'Berlin'),
('USA',		        'Los Angeles');


INSERT INTO Wyrok (status_winy, klasyfikacja, wiezienie_id) VALUES
('winny',	    'Kara więzienna lekka - do 1 roku',	5),
('winny',	    'Kara więzienna lekka - do 1 roku',	6),
('winny',	    'Kara śmierci',	                    7),
('winny',	    'Kara więzienna średnia - od 1 roku - 10 lat',	8),
('niewinny',	'Brak',	0),
('niewinny',	'Brak',	0),
('nie określono','Brak',0),
('nie określono','Brak',0),
('nie określono','Brak',0),
('nie określono','Brak',0);


INSERT INTO Przestepstwo (typ, motyw, data_przestepstwa, miejsce_id) VALUES 
('Wypadek samochodowy',                     'nieznany/brak','04-05-2019',	1),
('Morderstwo kochanka', 		            'Zdrada', 	    '12-09-2004',	2),
('Cyberprzemoc', 		                    'Zemsta', 	    '12-12-2007',	3),
('Wyłudzenie - "metoda na wnuczka"', 		'Zysk',      	'02-05-1997',	4),
('Zniszczenie własności',                   'Zemsta',   	'02-09-2011',	5),
('Naruszenie nietykalności funkcjonariusza','Zaburzenia psychiczne', '22-06-1998',	6),
('Rozbój w biały dzień', 		            'Szaleństwo', 	'01-01-2000',	1),
('Przestępczość zorganizowana', 	        'Zysk',     	'07-09-2014',	2),
('Produkcja amfetaminy', 		            'Zysk',      	'08-01-2020',	3),
('Kradzież z włamaniem', 		            'Zemsta', 	    '31-01-2002',	3);

INSERT INTO Narzedzie (numer_dowodu, miejsce_przechowania, narzedzie,  przestepstwo_id) VALUES
(55442 , 	'Warszawa', 	'samochód Volvo',       1),
(50442 , 	'Kraków', 	    'Młot',                 2),
(88002 , 	'Warszawa', 	'Laptop',               3),
(53255 , 	'Rzeszów', 	    'Telefon komórkowy',    4),
(54125 , 	'NY', 	        'Latarka',              6),
(46472 , 	'Berlin', 	    'Kij sosnowy',          6),
(91327 , 	'Londyn', 	    'Siekiera',             7),
(10007 , 	'Londyn', 	    'AK45',                 8),
(48868 , 	'Londyn', 	    'Beczka',               9),
(23744 , 	'Warszawa', 	'Łom',                  10),
(52847 , 	'Kraków', 	    'Lina grubość 40mm',    2),
(89282 , 	'Warszawa', 	'Rurka',                9);

INSERT INTO Poszkodowany (imie, nazwisko, straty, przestepstwo_id) VALUES
('Aleksander',  'Głowacki',	'Lekki uszczerbek na zdrowiu',      1),
('Brad',        'Pitt',	    'Śmierć',                           2),
('Jack',        'Nicholson','Uszczerbek na zdrowiu',            7),
('Leonadro',    'DiCaprio',	'Uszczerbek na zdrowiu',            7),
('Tom',         'Hanks',	'Materialne',                       10),
('Benedict',    'Cumberbatch','Materialne',                     10),
('Rami',	    'Malek',	'Śmierć',                           8),
('Bryan',	    'Cranston',	'Materialne',                       9);

INSERT INTO Oskarzony_przestepstwo (oskarzony_id, przestepstwo_id, wyrok_id) VALUES
(1,	1, 1),
(2,	2, 2),
(3,	3, 3),
(4,	4, 4),
(5,	5, 5),
(1,	6, 6),
(2,	7, 7),
(3,	8, 8),
(4,	9, 9),
(5,	10, 10);

