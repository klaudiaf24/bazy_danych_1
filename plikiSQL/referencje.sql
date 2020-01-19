ALTER TABLE Oskarzony ADD FOREIGN KEY (adwokat_id) REFERENCES Adwokat (id);

ALTER TABLE Oskarzony ADD FOREIGN KEY (prokurator_id) REFERENCES Prokurator (id);

ALTER TABLE Oskarzony_Przestepstwo ADD FOREIGN KEY (oskarzony_id) REFERENCES Oskarzony (id);

ALTER TABLE Oskarzony_Przestepstwo ADD FOREIGN KEY (przestepstwo_id) REFERENCES Przestepstwo (id);

ALTER TABLE Oskarzony_Przestepstwo ADD FOREIGN KEY (wyrok_id) REFERENCES Wyrok (id);

ALTER TABLE Przestepstwo ADD FOREIGN KEY (miejsce_id)  REFERENCES  Miejsce_przestepstwa (id);

ALTER TABLE Poszkodowany ADD FOREIGN KEY (przestepstwo_id) REFERENCES Przestepstwo (id);

ALTER TABLE Narzedzie ADD FOREIGN KEY (przestepstwo_id) REFERENCES Przestepstwo (id);

ALTER TABLE Swiadek ADD FOREIGN KEY (przestepstwo_id) REFERENCES  Przestepstwo (id);

ALTER TABLE Wyrok ADD FOREIGN KEY (wiezienie_id) REFERENCES  Wiezienie (id);
