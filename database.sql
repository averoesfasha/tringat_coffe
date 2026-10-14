CREATE DATABASE IF NOT EXISTS tringat_coffee CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tringat_coffee;

-- Akun admin. Password disimpan dalam bentuk hash, bukan teks biasa.
CREATE TABLE IF NOT EXISTS admin_users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(40) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS menu (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 description VARCHAR(255) DEFAULT '',
 price INT NOT NULL DEFAULT 0,
 category VARCHAR(60) DEFAULT 'Coffee',
 image_url VARCHAR(500) DEFAULT '',
 active TINYINT(1) NOT NULL DEFAULT 1
);
CREATE TABLE IF NOT EXISTS orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 queue_no INT NOT NULL,
 customer_name VARCHAR(120) NOT NULL,
 order_type ENUM('Dine In','Take Away') NOT NULL DEFAULT 'Take Away',
 total INT NOT NULL DEFAULT 0,
 status ENUM('Menunggu','Diproses','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS order_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 menu_id INT NULL,
 menu_name VARCHAR(120) NOT NULL,
 price INT NOT NULL,
 quantity INT NOT NULL,
 subtotal INT NOT NULL,
 temperature VARCHAR(10) DEFAULT NULL,
 sugar_level VARCHAR(20) DEFAULT NULL,
 FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
 FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE SET NULL
);

-- Harga di bawah hanya harga awal/contoh. Silakan sesuaikan di dashboard admin.
INSERT INTO menu (name,description,price,category) VALUES
('Tringat Coffee','Kopi khas Tringat Coffee',18000,'Coffee'),
('Coffee Susu Aren','Espresso, susu, dan gula aren',18000,'Coffee'),
('Caramel Latte','Espresso, susu, dan karamel',22000,'Coffee'),
('Cappuccino','Espresso dan susu berbusa',20000,'Coffee'),
('Hazelnut Latte','Espresso, susu, dan hazelnut',22000,'Coffee'),
('Americano','Espresso dengan air',15000,'Coffee'),
('Mochaccino','Kopi, susu, dan cokelat',22000,'Coffee'),
('Vanilla Latte','Espresso, susu, dan vanilla',22000,'Coffee'),
('Avocado Latte','Minuman alpukat dengan latte',23000,'Coffee'),
('Salted Creamy Latte','Latte dengan krim asin',23000,'Coffee'),
('Coffee Susu Creamy','Kopi susu creamy',20000,'Coffee'),
('Chocolate Caramel','Cokelat dan karamel',20000,'Non Coffee'),
('Red Velvet','Minuman red velvet creamy',20000,'Non Coffee'),
('Matcha Vanilla','Matcha dengan vanilla',22000,'Non Coffee'),
('Chocolate Hazelnut','Cokelat dan hazelnut',22000,'Non Coffee'),
('Avocado','Minuman alpukat',20000,'Non Coffee'),
('Ocean Blues','Mocktail biru menyegarkan',18000,'Mocktail'),
('Mango Blue','Mocktail mangga dan blue',18000,'Mocktail'),
('Lychee Mocktail','Mocktail leci',18000,'Mocktail'),
('Americano Grass','Mocktail/minuman Americano Grass',18000,'Mocktail'),
('Tea','Teh',10000,'Tea Series'),
('Lychee Tea','Teh leci',15000,'Tea Series'),
('Lemon Tea','Teh lemon',13000,'Tea Series'),
('Es Batu','Tambahan es batu',2000,'Add On'),
('Espresso','Tambahan satu shot espresso',7000,'Add On'),
('Sugar','Tambahan gula',2000,'Add On'),
('Cup','Gelas tambahan',2000,'Add On'),
('Mineral Water','Air mineral',5000,'Add On'),
('Mix Platter','Camilan campur',25000,'Snack'),
('French Fries','Kentang goreng',18000,'Snack'),
('Otak-Otak Bandung','Otak-otak Bandung',15000,'Snack'),
('Dimsum','Dimsum',18000,'Snack'),
('Banana Crispy','Pisang crispy',16000,'Snack');
