CREATE OR REPLACE FUNCTION moje_dane_prokurator (email varchar, haslo varchar)
RETURNS SETOF kartoteka.prokurator 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * 
		FROM kartoteka.prokurator p
		WHERE p.email = $1 and p.haslo = $2;
END;
';

CREATE OR REPLACE FUNCTION moje_dane_adwokat (email varchar, haslo varchar)
RETURNS SETOF kartoteka.adwokat 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * 
		FROM kartoteka.adwokat p
		WHERE p.email = $1 and p.haslo = $2;
END;
';

CREATE OR REPLACE FUNCTION moje_dane_oskarzony (email varchar, haslo varchar)
RETURNS SETOF kartoteka.oskarzony 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * 
		FROM kartoteka.oskarzony p
		WHERE p.email = $1 and p.haslo = $2;
END;
';

CREATE OR REPLACE FUNCTION moi_oskarzeni_prokurator (id int)
RETURNS SETOF kartoteka.oskarzony 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * 
		FROM kartoteka.oskarzony o
		 WHERE o.prokurator_id = $1;
END;
';

CREATE OR REPLACE FUNCTION moi_oskarzeni_adwokat (id int)
RETURNS SETOF kartoteka.oskarzony 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * 
		FROM kartoteka.oskarzony o
		 WHERE o.adwokat_id = $1;
END;
';


CREATE VIEW wszyscy_oskarzeni AS
SELECT * 
FROM kartoteka.oskarzony;

CREATE VIEW wszyscy_prokuratorzy AS
SELECT * 
FROM kartoteka.prokurator;


CREATE VIEW wszyscy_adwokaci AS
SELECT * 
FROM kartoteka.adwokat;

CREATE VIEW wszystkie_wiezienia AS
SELECT * 
FROM kartoteka.wiezienie;

CREATE VIEW narzedzia_baza AS
SELECT p.typ, p.data_przestepstwa, n.narzedzie, n.numer_dowodu, n.miejsce_przechowania 
        FROM kartoteka.narzedzie n 
		LEFT JOIN kartoteka.przestepstwo p 
		ON n.przestepstwo_id = p.id 
        ORDER BY n.numer_dowodu;

CREATE OR REPLACE FUNCTION moje_sprawy_prokurator (idp int)
RETURNS SETOF kartoteka.przestepstwo 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * FROM kartoteka.przestepstwo p 
                WHERE p.id IN (	SELECT przestepstwo_id 
                                FROM kartoteka.oskarzony_przestepstwo op
                                WHERE op.oskarzony_id IN (   SELECT id
                                                            FROM kartoteka.oskarzony o
                                                            WHERE o.prokurator_id = $1
                                                        )
                            );
END;
';


CREATE OR REPLACE FUNCTION moje_sprawy_adwokat (ida int)
RETURNS SETOF kartoteka.przestepstwo 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * FROM kartoteka.przestepstwo p 
                WHERE p.id IN (	SELECT przestepstwo_id 
                                FROM kartoteka.oskarzony_przestepstwo op
                                WHERE op.oskarzony_id IN (   SELECT id
                                                            FROM kartoteka.oskarzony o
                                                            WHERE o.adwokat_id = $1
                                                        )
                            );
END;
';

CREATE OR REPLACE FUNCTION adwokat_oskarzonego (ido int)
RETURNS SETOF kartoteka.adwokat 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * FROM kartoteka.adwokat a
                WHERE a.id = $1;
END;
';

CREATE OR REPLACE FUNCTION wszystkie_moje_przestepstwa_oskarzony (ido int)
RETURNS SETOF kartoteka.przestepstwo 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * FROM kartoteka.przestepstwo p 
		WHERE p.id IN (	SELECT przestepstwo_id 
				FROM kartoteka.oskarzony_przestepstwo op 
				WHERE op.oskarzony_id = $1);

END;
';


CREATE OR REPLACE FUNCTION narzedzia_w_przestepstwie (idp int)
RETURNS SETOF kartoteka.narzedzie 
LANGUAGE plpgsql 
AS '
BEGIN
   RETURN QUERY	
		SELECT * FROM kartoteka.narzedzie n
		WHERE n.przestepstwo_id = $1;
END;
';




CREATE VIEW ostatni_wyrok AS
SELECT * 
FROM kartoteka.wyrok 
ORDER BY id DESC
LIMIT 1;

CREATE VIEW ostatnie_przestepstwo AS
SELECT * 
FROM kartoteka.przestepstwo 
ORDER BY id DESC
LIMIT 1;

CREATE VIEW ostatni_oskarzony AS
SELECT * 
FROM kartoteka.oskarzony 
ORDER BY id DESC
LIMIT 1;

CREATE OR REPLACE FUNCTION usun_oskarzonego (ido int)
RETURNS void
LANGUAGE plpgsql 
AS '
BEGIN
	DELETE FROM kartoteka.oskarzony_przestepstwo WHERE oskarzony_id = $1;
	DELETE FROM kartoteka.oskarzony WHERE id = $1;
END;
';

CREATE OR REPLACE FUNCTION ilosc_osadzonych_wzgl_klasyfikacji (idwiezienia int)
RETURNS TABLE( klas kartoteka.klasyfikacja_wyroku, total bigint)
AS '
BEGIN
   RETURN QUERY	
		SELECT klasyfikacja, COUNT(id) FROM kartoteka.wyrok w
		WHERE w.wiezienie_id = $1 GROUP BY klasyfikacja;

END;
'
LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION wyroki_w_sprawach_adwokat (idadwokat int)
RETURNS SETOF kartoteka.wyrok 
AS '
BEGIN
   RETURN QUERY	
		SELECT * FROM kartoteka.wyrok w
				WHERE w.id IN (	SELECT wyrok_id 
								FROM kartoteka.oskarzony_przestepstwo op
								WHERE op.oskarzony_id IN (	SELECT id
															FROM kartoteka.oskarzony o
															WHERE o.adwokat_id = $1
															)
								);

END;
'
LANGUAGE plpgsql ;


CREATE OR REPLACE FUNCTION staty_wyroki_adwokat (idadwokat int)
RETURNS TABLE( klas kartoteka.klasyfikacja_wyroku, total bigint)
AS '
BEGIN
   RETURN QUERY	
		SELECT klasyfikacja, COUNT(id) as total
                                    FROM kartoteka.wyrok w
                                    WHERE w.id IN ( SELECT wyrok_id 
                                                    FROM kartoteka.oskarzony_przestepstwo op 
                                                    WHERE op.oskarzony_id IN (   SELECT id
                                                                                        FROM kartoteka.oskarzony o
                                                                                        WHERE o.adwokat_id = $1))
                                    GROUP BY klasyfikacja
                                    ;

END;
'
LANGUAGE plpgsql ;

CREATE OR REPLACE FUNCTION usun_przestepstwo (idp int)
RETURNS void
AS '
BEGIN
	DELETE FROM kartoteka.oskarzony_przestepstwo WHERE przestepstwo_id = $1;
	DELETE FROM kartoteka.poszkodowany WHERE przestepstwo_id = $1;
	DELETE FROM kartoteka.wyrok WHERE id IN (SELECT wyrok_id FROM kartoteka.oskarzony_przestepstwo WHERE przestepstwo_id = $1);
	DELETE FROM kartoteka.narzedzie WHERE przestepstwo_id = $1;
	DELETE FROM kartoteka.swiadek WHERE przestepstwo_id = $1;
	DELETE FROM kartoteka.przestepstwo WHERE id = $1;
END;
'
LANGUAGE plpgsql ;


