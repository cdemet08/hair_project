


INSERT INTO User (email,name,lastname,phone,age,male,adminuser,password) VALUES ('cdemets','ch','last',1256622,22,1,1,'really');
INSERT INTO User (email,name,lastname,phone,age,male,adminuser,password) VALUES ('cdemet','ch','last',1256622,22,1,0,'epl425');

INSERT INTO Barber (name,lastname,phone,email,male,age) VALUES ('cde','ch',1256622,'chemail',1,59);

INSERT INTO Appointment (date,idUser,idBarber) VALUES ('2016-05-02 01:12:30',1,2);

INSERT INTO Offer (Name,Price,Description) VALUES ('2016-05-02 01:12:30',1,2);

INSERT INTO Price (name,price,description) VALUES ('2016-05-02 01:12:30',1,2);



delete from Price where price=10;