CREATE SCHEMA Kartoteka;
SET SEARCH_PATH TO Kartoteka;

CREATE TYPE klasyfikacja_wyroku AS ENUM (
  'Brak',
  'Kara pieniężna',
  'Wyrok w zawieszeniu',
  'Kara więzienna lekka - do 1 roku',
  'Kara więzienna średnia - od 1 roku - 10 lat',
  'Kara więzienna wysoka - od 10 lat',
  'Kara śmierci'
);

CREATE TYPE typ_wiezienia AS ENUM (
  'brak',
  'zamknięte',
  'półotwarte',
  'otwarte'
);

CREATE TYPE st_winy AS ENUM (
  'winny',
  'niewinny',
  'nie określono'
);

CREATE TYPE poszkodowany_straty AS ENUM (
  'Materialne',
  'Lekki uszczerbek na zdrowiu',
  'Uszczerbek na zdrowiu',
  'Znaczący uszczerbek na zdrowiu',
  'Śmierć'
);

CREATE TABLE Oskarzony (
  id SERIAL PRIMARY KEY,
  imie varchar,
  nazwisko varchar,
  email varchar,
  haslo varchar,
  adwokat_id int,
  prokurator_id int
);

CREATE TABLE Adwokat (
  id SERIAL PRIMARY KEY,
  imie varchar,
  nazwisko varchar,
  email varchar,
  haslo varchar,
  nazwa_kancelarii varchar,
  numer_licencji numeric(5,0) UNIQUE
);

CREATE TABLE Prokurator (
  id SERIAL PRIMARY KEY,
  imie varchar,
  nazwisko varchar,
  email varchar,
  haslo varchar,
  miejsce_pracy varchar,
  numer_licencji numeric(5,0) UNIQUE
);

CREATE TABLE Przestepstwo (
  id SERIAL PRIMARY KEY,
  typ varchar,
  motyw varchar,
  data_przestepstwa date,
  miejsce_id int
);

CREATE TABLE Poszkodowany (
  id SERIAL PRIMARY KEY,
  imie varchar,
  nazwisko varchar,
  straty poszkodowany_straty,
  przestepstwo_id int
);

CREATE TABLE Swiadek (
  id SERIAL PRIMARY KEY,
  imie varchar,
  nazwisko varchar,
  przestepstwo_id int
);

CREATE TABLE Miejsce_przestepstwa (
  id SERIAL PRIMARY KEY,
  kraj varchar,
  miasto varchar
);

CREATE TABLE Narzedzie (
  id SERIAL PRIMARY KEY,
  narzedzie varchar,
  numer_dowodu numeric(5,0) UNIQUE,
  miejsce_przechowania varchar,
  przestepstwo_id int
);

CREATE TABLE Wyrok (
  id SERIAL PRIMARY KEY,
  status_winy st_winy DEFAULT 'nie określono',
  klasyfikacja klasyfikacja_wyroku,
  wiezienie_id int
);

CREATE TABLE Wiezienie (
  id SERIAL PRIMARY KEY,
  kraj varchar,
  miasto varchar,
  typ typ_wiezienia
);

CREATE TABLE Oskarzony_przestepstwo (
  oskarzony_id int,
  przestepstwo_id int,
  wyrok_id int
);


