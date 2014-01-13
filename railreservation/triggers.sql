CREATE OR REPLACE FUNCTION check_userid() RETURNS TRIGGER AS $BODY$
	DECLARE 
		tablename TEXT;
		flag INT;
		tempid TEXT;
		id TEXT;
	BEGIN
		flag=0;
		id=NEW.userid;
		tempid='wow';
		FOR i in 1..26 LOOP
			tablename='User_'||i;
			id='SELECT '||tablename||'.userid from '||tablename||' WHERE '||tablename||'.userid='||NEW.userid;
			IF id IS NOT NULL THEN
				EXECUTE id INTO tempid;
			END IF;	 
			IF tempid IS NOT NULL THEN
				flag=1;
			END IF;	
		END LOOP;
		IF (flag=1) THEN
			RETURN NEW;
		END IF;
		RAISE EXCEPTION 'userid % doesn''t exist!', NEW.userid;	
	END;
$BODY$
LANGUAGE 'plpgsql' ;

CREATE OR REPLACE FUNCTION check_resid() RETURNS TRIGGER AS $BODY$
	DECLARE 
		tablename TEXT;
		flag INT;
		tempid TEXT;
		id TEXT;
	BEGIN
		flag=0;
		id=NEW.resid;
		tempid='wow';
		FOR i in 1..120 LOOP
			tablename='Trip_'||i;
			id='SELECT '||tablename||'.userid from '||tablename||' WHERE '||tablename||'.resid='||NEW.resid;
			IF id IS NOT NULL THEN
				EXECUTE id INTO tempid;
			END IF;	 
			IF tempid IS NOT NULL THEN
				flag=1;
			END IF;	
		END LOOP;
		IF (flag=1) THEN
			RETURN NEW;
		END IF;
		RAISE EXCEPTION 'resid % doesn''t exist!', NEW.resid;	
	END;
$BODY$
LANGUAGE 'plpgsql' ;

--userid TRIGGER FOR TDR TABLE
DROP TRIGGER IF EXISTS usertdrtrigger on Tdr;
CREATE TRIGGER usertdrtrigger BEFORE INSERT OR UPDATE ON Tdr
FOR EACH ROW EXECUTE PROCEDURE check_userid();

--resid TRIGGER FOR TDR TABLE
DROP TRIGGER IF EXISTS residtdrtrigger on Tdr;
CREATE TRIGGER usertdrtrigger BEFORE INSERT OR UPDATE ON Tdr
FOR EACH ROW EXECUTE PROCEDURE check_resid();

--resid TRIGGER FOR TDRRESID TABLE
DROP TRIGGER IF EXISTS residtdrresidtrigger on Tdrresid;
CREATE TRIGGER residtdrresidtrigger BEFORE INSERT OR UPDATE ON Tdrresid
FOR EACH ROW EXECUTE PROCEDURE check_resid();

--userid,resid TRIGGERS GENERATION FOR RESERVATION TABLES
CREATE OR REPLACE FUNCTION create_reservation_tables_triggers() RETURNS void AS 
$BODY$
	DECLARE 	
		tablename TEXT;
		triggername TEXT;
	BEGIN 
		FOR i in 1..26 LOOP
				tablename='Reservation'||i;
				triggername=tablename||'useridtrigger';
				EXECUTE'
				DROP TRIGGER IF EXISTS '||triggername||' on '||tablename||'
				';
				EXECUTE '
				CREATE TRIGGER '||triggername||' BEFORE INSERT OR UPDATE ON '||tablename||' FOR EACH ROW EXECUTE PROCEDURE check_userid() 
				';
				triggername=tablename||'residtrigger';
				EXECUTE'
				DROP TRIGGER IF EXISTS '||triggername||' on '||tablename||'
				';
				EXECUTE '
				CREATE TRIGGER '||triggername||' BEFORE INSERT OR UPDATE ON '||tablename||' FOR EACH ROW EXECUTE PROCEDURE check_resid() 
				';
		END LOOP;
		RETURN ;
	END;
$BODY$
LANGUAGE 'plpgsql' ;
SELECT create_reservation_tables_triggers();


