DROP TABLE Inventory;
DROP TABLE OrderParts;
DROP TABLE Orders;
DROP TABLE ShippingCharges;

/*
Legacy part db:

create table parts ( 
    number int, 
    description varchar(50), 
    price float(8,2), 
    weight float(4,2), 
    pictureURL varchar(50)
 );
*/

CREATE TABLE Inventory (
    partNum INT PRIMARY KEY,
    quantity INT
);

CREATE TABLE Orders (
    orderNum INT PRIMARY KEY AUTO_INCREMENT,
    timePlaced TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status CHAR(10),
    totalPrice DECIMAL(9,2)
);

CREATE TABLE OrderParts (
    orderNum INT NOT NULL,
    partNum INT NOT NULL,
    quantity INT NOT NULL,

    FOREIGN KEY(orderNum) REFERENCES Orders(orderNum)
);

CREATE TABLE ShippingCharges (
    bracketName CHAR(15) PRIMARY KEY,
    weightCutoff DECIMAL(5,2),
    charge DECIMAL(4,2)
);

INSERT INTO Inventory VALUES(1, 11);
INSERT INTO Inventory VALUES(2, 22);
INSERT INTO Inventory VALUES(3, 33);
INSERT INTO Inventory VALUES(4, 44);

INSERT INTO Orders VALUES(1, DEFAULT, "placed", 111.11);
INSERT INTO Orders VALUES(2, DEFAULT, "authorized", 222.22);
INSERT INTO Orders VALUES(3, DEFAULT, "shipped", 333.33);

INSERT INTO OrderParts VALUES(1, 1, 1);
INSERT INTO OrderParts VALUES(1, 2, 2);
INSERT INTO OrderParts VALUES(2, 3, 3);
INSERT INTO OrderParts VALUES(3, 4, 4);

INSERT INTO ShippingCharges VALUES("LOW", 5.0, 5.00);
INSERT INTO ShippingCharges VALUES("MEDIUM", 15.0, 10.00);
INSERT INTO ShippingCharges VALUES("HIGH", 50.0, 15.00);
INSERT INTO ShippingCharges VALUES("SUPER", 500.0, 25.00);
