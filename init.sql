CREATE DATABASE IF NOT EXISTS pizza_db;

CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'app_password';

GRANT ALL PRIVILEGES ON pizza_db.* TO 'app_user'@'%';

FLUSH PRIVILEGES;

USE pizza_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    house_nr VARCHAR(255) NOT NULL,
    postal_code VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS pizza_dough (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    price INT NOT NULL,
    image_url VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    image_url VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    dough_id INT NOT NULL,
    extra_wish VARCHAR(255) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (dough_id) REFERENCES pizza_dough(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS order_item_toppings (
    order_item_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    PRIMARY KEY (order_item_id, ingredient_id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);



INSERT INTO users (username, password, firstname, lastname, street, house_nr, postal_code)
VALUES 
('demo_user', 'demo_password', 'John', 'Doe', 'Pizza Street', '123', '77723');

INSERT INTO pizza_dough (name, price, image_url)
VALUES
('tomato', 800, 'images/dough/tomato.jpg'),
('olive-oil', 800, 'images/dough/olive-oil.jpg'),
('sour-cream', 800, 'images/dough/sour-cream.jpg'),
('herbs-garlic', 800, 'images/dough/herbs-garlic.jpg');

INSERT INTO ingredients (name, price, image_url)
VALUES
('mozzarella', 200, 'images/toppings/mozzarella.jpg'),
('olives', 200, 'images/toppings/olives.jpg'),
('ruccula', 200, 'images/toppings/ruccula.jpg'),
('salami', 200, 'images/toppings/salami.jpg'),
('parma', 450, 'images/toppings/parma.jpg'),
('mushrooms', 300, 'images/toppings/mushrooms.jpg'),
('eggplant', 250, 'images/toppings/eggplant.jpg'),
('peccorino', 250, 'images/toppings/peccorino.jpg'),
('chilli', 250, 'images/toppings/chilli.jpg'),
('zucchini', 250, 'images/toppings/zucchini.jpg');
