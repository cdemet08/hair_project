


INSERT INTO User (email,name,lastname,phone,age,male,adminuser,password) VALUES ('cdemets','ch','last',1256622,22,1,1,'really');
INSERT INTO User (email,name,lastname,phone,age,male,adminuser,password) VALUES ('cdemet','ch','last',1256622,22,1,0,'epl425');
INSERT INTO User (email,name,lastname,phone,age,male,adminuser,password) VALUES ('cdemet22','ch','last',125662,22,'M','N','epl425');


INSERT INTO Barber (name,lastname,phone,email,male,age) VALUES ('Trump','ch',1256622,'chemail',1,56);



INSERT INTO Appointment (dateTimeApp,idUser,idBarber,typeAppointment) VALUES ('2016-11-19 01:12:30',1,2,"man_cut");


INSERT INTO Offer (Name,Price,Description) VALUES ('2016-11-02 01:12:30',1,2);

INSERT INTO Price (name,price,description) VALUES ('man_cut',10,"Man cut");


INSERT INTO Workday (workday,startday,endday) VALUES ('Saturday','2016-11-19 09:00:00','2016-11-19 18:00:00');


delete from Price where price=10;

UPDATE Barber SET age='22' WHERE idBarber=6;

SELECT a.idAppointment ,a.dateTimeApp , b.name AS barberName FROM `Appointment` AS a INNER JOIN `Barber` AS b ON a.idBarber = b.idBarber;
