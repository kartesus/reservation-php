DROP TABLE IF EXISTS waitlist;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS table_types;
DROP TABLE IF EXISTS emails;

CREATE TABLE table_types (
    id INT PRIMARY KEY AUTO_INCREMENT, 
    name VARCHAR(255) NOT NULL, 
    capacity INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_type_id INT,
    customer_id INT,
    reservation_date DATE,
    status VARCHAR(255) DEFAULT 'pending',
    FOREIGN KEY (table_type_id) REFERENCES  table_types(id),
    FOREIGN KEY (customer_id) REFERENCES  customers(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS emails (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO table_types (name, capacity) VALUES ('outdoor', 4);
INSERT INTO table_types (name, capacity) VALUES ('indoor', 4);

INSERT INTO customers (name, email) VALUES ('Thomas Hobbes', 'hobbes@leviathanstate.org');
INSERT INTO customers (name, email) VALUES ('Francis Bacon', 'bacon@scientificmethod.co.uk');
INSERT INTO customers (name, email) VALUES ('Jeremy Bentham', 'bentham@utilitarianismforall.org');
INSERT INTO customers (name, email) VALUES ('Edmund Burke', 'burke@conservatism.com');
INSERT INTO customers (name, email) VALUES ('Adam Smith', 'smith@wealthofnations.biz');
INSERT INTO customers (name, email) VALUES ('David Hume', 'hume@empiricism.com');
INSERT INTO customers (name, email) VALUES ('John Locke', 'locke@tabularasa.net');


INSERT INTO reservations (table_type_id, customer_id, reservation_date) VALUES (2, 1, '2023-05-20');
INSERT INTO reservations (table_type_id, customer_id, reservation_date) VALUES (2, 2, '2023-05-20');
INSERT INTO reservations (table_type_id, customer_id, reservation_date) VALUES (2, 3, '2023-05-20');
INSERT INTO reservations (table_type_id, customer_id, reservation_date) VALUES (1, 4, '2023-05-20');
INSERT INTO reservations (table_type_id, customer_id, reservation_date) VALUES (1, 5, '2023-05-20');