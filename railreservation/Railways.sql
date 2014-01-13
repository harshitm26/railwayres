DROP DATABASE IF EXISTS railres;
CREATE DATABASE railres;


\c railres
\o ~/output.txt
--_____________________________________________________________________________DOMAINS______________________________________

CREATE DOMAIN GENDER AS varchar(6)
CHECK (
	VALUE SIMILAR TO ('Male|Female')
	);
	
CREATE DOMAIN DAYS AS varchar(8)
CHECK (
	VALUE SIMILAR TO ('Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday')
	);
	
CREATE DOMAIN TRAINTYPE AS varchar(3)
CHECK(
	VALUE SIMILAR TO ('EXP|GAR|JAN|SHA|RAJ|LOC')
	);

CREATE DOMAIN COACHTYPE AS varchar(2)
CHECK (
	VALUE SIMILAR TO ('SL|A|B|C|CC|G')
	);
	
CREATE DOMAIN QUOTA AS varchar(3)
CHECK (
	VALUE SIMILAR TO ('TKL|GN|LD')
	);

CREATE DOMAIN YN AS char(1)
CHECK (
	VALUE SIMILAR TO ('Y|N')
	);

CREATE DOMAIN STATUS AS varchar(3)
CHECK (
	VALUE SIMILAR TO ('CNF|WL|RAC')
	);
	
--~ CREATE DOMAIN TDRREASON AS char(2)
--~ CHECK(
	--~ VALUE SIMILAR TO('NC|TC')
	--~ );
	
--__________________________________________SEQUENCES__________________________________________
CREATE SEQUENCE pnrcntr START 1;
CREATE SEQUENCE residcntr START 1;
CREATE SEQUENCE tdrcntr START 1;
--_________________________________________USER DATA___________________________________________________
CREATE SEQUENCE userid START 1000000;

CREATE FUNCTION create_user_tables() RETURNS void AS 
$BODY$
	DECLARE 
		tablename TEXT;
	BEGIN 
		FOR i in 1..26 LOOP
				tablename='User_'||i;
				EXECUTE '
				CREATE TABLE '|| tablename||'(
					userid varchar(50) NOT NULL, 
					pwd varchar(30) NOT NULL, 
					contact char(10),
					firstname varchar(25) NOT NULL,
					lastname varchar(25),
					emailid varchar(40),
					gender GENDER NOT NULL, 
					dob DATE NOT NULL,
					PRIMARY KEY (userid)
					)';
				EXECUTE 'COPY User_'||i||' FROM ''/home/vinit/WEBSITES/railways/railres/Data/Userdata/userdata_'||i||'.csv'' CSV';
		END LOOP;
		RETURN ;
	END;
$BODY$
LANGUAGE 'plpgsql' ;
SELECT create_user_tables();

--_______________________	 TRAINS ___________________________________________________________________________________


CREATE TABLE Train(
	trainno integer NOT NULL,
	trname varchar(60) NOT NULL,
	trtype TRAINTYPE NOT NULL,
	npantry smallint NOT NULL CONSTRAINT valid_pantry CHECK (npantry BETWEEN 0 AND 4), 
	ptatkalquota smallint NOT NULL CONSTRAINT valid_tatkal_quota CHECK (ptatkalquota BETWEEN 0 AND 100), 
	pladiesquota smallint NOT NULL CONSTRAINT valid_ladies_quota CHECK (pladiesquota BETWEEN 0 AND 100),
	pracres smallint NOT NULL CONSTRAINT valid_rac_quota CHECK (pracres BETWEEN 0 AND 100),
	pwlres smallint NOT NULL CONSTRAINT valid_waiting_list CHECK (pwlres BETWEEN 0 AND 300),
	CHECK( ptatkalquota + pladiesquota + pracres <100),
	PRIMARY KEY (trainno)
	);
	
COPY Train FROM '/home/vinit/WEBSITES/railways/railres/Data/Train/rajdhanidata.csv' CSV ;
COPY Train FROM '/home/vinit/WEBSITES/railways/railres/Data/Train/expressdata.csv' CSV ;
COPY Train FROM '/home/vinit/WEBSITES/railways/railres/Data/Train/shatabdidata.csv' CSV ;
COPY Train FROM '/home/vinit/WEBSITES/railways/railres/Data/Train/janshatabdidata.csv' CSV ;
COPY Train FROM '/home/vinit/WEBSITES/railways/railres/Data/Train/garibrathdata.csv' CSV ;

--_______________________________________POOL_______________________________________________________________________

CREATE TABLE Pool(
	trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE RESTRICT, 
	stno smallint NOT NULL CONSTRAINT valid_stno CHECK(stno >= 0), 
	nsleep smallint NOT NULL CONSTRAINT valid_nsleep CHECK(nsleep >= 0), 
	ngen smallint NOT NULL CONSTRAINT valid_ngen CHECK(nsleep >= 0),
	nfac smallint NOT NULL CONSTRAINT valid_nfac CHECK(nfac >= 0), 
	nsac smallint NOT NULL CONSTRAINT valid_nsac CHECK(nsac >= 0), 
	ntac smallint NOT NULL CONSTRAINT valid_ntac CHECK(ntac >= 0), 
	ncc smallint NOT NULL CONSTRAINT valid_ncc CHECK(ncc >= 0),
	PRIMARY KEY(stno, trainno)
	);

COPY Pool FROM '/home/vinit/WEBSITES/railways/railres/Data/Pool/pool.csv' CSV ;

--________________________________________________________TRAIN_DAYS_______________________________________________
CREATE TABLE Train_days(
	trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE CASCADE, 
	days smallint NOT NULL CONSTRAINT valid_days CHECK (days BETWEEN 0 AND 6),
	PRIMARY KEY (trainno, days)
	);

COPY Train_days FROM '/home/vinit/WEBSITES/railways/railres/Data/Trdays/trdays.csv' CSV ;
--__________________________________________________________________STATION________________________________________	
CREATE TABLE Station(
	stcode varchar(6) NOT NULL, 
	stname varchar(25) NOT NULL, 
	PRIMARY KEY(stcode)
	);

COPY Station FROM '/home/vinit/WEBSITES/railways/railres/Data/stcodestname.csv' CSV ;
--___________________________________________________________________STAND__________________________________________
CREATE TABLE Stand(
	trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE CASCADE, 
	stno smallint NOT NULL,
	stcode varchar(5) NOT NULL REFERENCES Station(stcode) ON DELETE RESTRICT, 
	dtarr TIME NOT NULL,
	dtdep TIME NOT NULL,
	dayoffset SMALLINT NOT NULL CONSTRAINT valid_dayoffset CHECK(dayoffset >= 0),
	distance int NOT NULL CONSTRAINT valid_distance CHECK(distance >= 0),
	PRIMARY KEY(trainno, stno)
	);

COPY Stand FROM '/home/vinit/WEBSITES/railways/railres/Data/Stands/stands.csv' CSV ;

--________________________________________________FARE_________________________________________________
CREATE TABLE Fare(
	trtype TRAINTYPE NOT NULL, 
	chtype COACHTYPE NOT NULL, 
	quota QUOTA NOT NULL DEFAULT 'GN',
	costperkm real NOT NULL CONSTRAINT valid_costperkm CHECK(costperkm > 0.0),  
	cancharges smallint NOT NULL CONSTRAINT valid_cancharges CHECK(cancharges >= 5),
	PRIMARY KEY(trtype, chtype, quota)
	);
	
COPY Fare FROM '/home/vinit/WEBSITES/railways/railres/Data/Fare/Fare.csv' CSV;

--_________________________________________________	TDR_________________________________________
CREATE TABLE Tdr(
	tdrno integer NOT NULL DEFAULT NEXTVAL('tdrcntr'),
	reason TEXT NOT NULL,
	dtfile TIMESTAMP NOT NULL, 
	userid VARCHAR(50) NOT NULL, --constraint to be checket at application level--done
	refund SMALLINT, 
	PRIMARY KEY (tdrno)
	);

--____________________________________________TDR RES ID__________________________________
	
CREATE TABLE Tdrresid(
	tdrno INTEGER REFERENCES Tdr(tdrno) ON DELETE RESTRICT, 
	resid INTEGER NOT NULL, --constraint to be checked at application level--done
	PRIMARY KEY (tdrno, resid)
	);



--________________________________________________TICKET USER_____________________________________

CREATE FUNCTION create_tkt_tables() RETURNS void AS 
$BODY$
	DECLARE 	
		tablename TEXT;
	BEGIN 
		FOR i in 1..26 LOOP
				tablename='Tkt_'||i;
				EXECUTE '
				CREATE TABLE '|| tablename||'(
					pnr INTEGER DEFAULT NEXTVAL(''pnrcntr'') , 
					cost REAL NOT NULL CONSTRAINT valid_cost CHECK(cost >= 5), 
					boardstno SMALLINT NOT NULL, 
					fromstno SMALLINT NOT NULL, 
					tostno SMALLINT NOT NULL, 
					trainno INTEGER NOT NULL REFERENCES Train(trainno), 
					dtjour TIMESTAMP NOT NULL, 
					dtbook TIMESTAMP NOT NULL, 
					userid VARCHAR(50) NOT NULL,  
					distance INTEGER NOT NULL CONSTRAINT valid_distance CHECK( distance > 0),
					CHECK (tostno>fromstno),
					PRIMARY KEY(pnr),
					FOREIGN KEY(trainno, boardstno) REFERENCES Stand(trainno, stno) ON DELETE CASCADE, 
					FOREIGN KEY (trainno, fromstno) REFERENCES Stand(trainno, stno) ON DELETE CASCADE, 
					FOREIGN KEY (trainno, tostno) REFERENCES Stand(trainno, stno) ON DELETE CASCADE
					)';
					--Table constraint is used
					--check constraint for userid at application level
		END LOOP;
		RETURN ;
	END;
$BODY$
LANGUAGE 'plpgsql' ;
SELECT create_tkt_tables();



/*

--________________________________________________SEAT AVAILABILTY ____________________________________________	
CREATE FUNCTION create_seatavl_tables() RETURNS void AS 
$BODY$
	DECLARE 	
		tablename TEXT;
	BEGIN 
		FOR i in 1..120 LOOP
				tablename='Seatavlbl_'||i;
				EXECUTE '
				CREATE TABLE '|| tablename||'(
					trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE RESTRICT,
					seatno1 smallint NOT NULL CONSTRAINT valid_seatno1 CHECK(seatno1 > 0), 
					seatno2 smallint NOT NULL CONSTRAINT valid_seatno2 CHECK(seatno1 > 0), 
					PRIMARY KEY (trainno, seatno1)
					)';
		END LOOP;
		RETURN ;
	END;
$BODY$
LANGUAGE 'plpgsql' ;
SELECT create_seatavl_tables();
--____________________________________________TRIP DATE______________________________________


CREATE FUNCTION create_trip_tables() RETURNS void AS 
$BODY$
	DECLARE 	
		tablename TEXT;
	BEGIN 
		FOR i in 1..120 LOOP
				tablename='Trip_'||i;
				EXECUTE '
				CREATE TABLE '|| tablename||'(
					resid integer NOT NULL DEFAULT NEXTVAL(''residcntr''),
					pnr INTEGER NOT NULL, 
					trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE RESTRICT,
					cancelled YN NOT NULL, 
					pool smallint NOT NULL, 
					quota QUOTA NOT NULL , 
					status STATUS NOT NULL, 
					coach COACHTYPE NOT NULL, 
					seat smallint CONSTRAINT valid_seat CHECK (seat BETWEEN 1 AND 72),
					passname_first varchar(20) NOT NULL, 
					passname_last varchar(20), 
					passgender GENDER NOT NULL, 
					passage smallint CONSTRAINT valid_age CHECK (passage > 0), 
					PRIMARY KEY (resid) 
					)';
		END LOOP;
		RETURN ;
	END;
$BODY$
LANGUAGE 'plpgsql' ;
SELECT create_trip_tables();

--___________________________________________________TRIGGER FOR CHECKING WHETHER THE pnr INSERTED IN TRIP BELONGS TO SOME TKT______________________



--_________________________________________STATS DATE_____________________________________


CREATE FUNCTION create_stats_tables() RETURNS void AS 
$BODY$
	DECLARE 	
		tablename TEXT;
	BEGIN 
		FOR i in 1..120 LOOP
				tablename='Stats_'||i;
				EXECUTE '
				CREATE TABLE '|| tablename||'(
					trainno integer NOT NULL REFERENCES Train(trainno) ON DELETE RESTRICT, 
					pool smallint NOT NULL, 
					chtype COACHTYPE NOT NULL, 
					quota QUOTA NOT NULL, 
					status STATUS NOT NULL, 
					nseats smallint NOT NULL, 	
					PRIMARY KEY (trainno, pool , chtype, quota, status)
					)';
		END LOOP;
		RETURN ;
	END;
$BODY$
LANGUAGE 'plpgsql' ;
SELECT create_stats_tables();


*/
