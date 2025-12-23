-- install.sql for endterm database

CREATE DATABASE IF NOT EXISTS endterm;
USE endterm;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS favourites;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS flowers;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(255),
    profile_image VARCHAR(255),
    password VARCHAR(255),
    role VARCHAR(50)
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);

CREATE TABLE flowers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    sku VARCHAR(100),
    category_id INT,
    price DECIMAL(10,2),
    stock INT,
    color VARCHAR(100),
    description TEXT,
    image VARCHAR(255),
    visible TINYINT(1),
    keywords TEXT
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flower_id INT,
    quantity INT
);

CREATE TABLE favourites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    flower_id INT
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(10,2),
    status VARCHAR(50)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    flower_id INT,
    quantity INT,
    price_at_order DECIMAL(10,2)
);

-- Seed categories
INSERT INTO categories (id, name) VALUES
(1, 'Asteraceae'),
(2, 'Orchidaceae'),
(3, 'Fabaceae'),
(4, 'Rosaceae'),
(5, 'Lamiaceae'),
(6, 'Liliaceae'),
(7, 'Brassicaceae');

-- Seed users (admin only)
INSERT INTO users (id, username, email, profile_image, password, role) VALUES
(2, 'admin', 'admin@example.com', 'images/lilly_info_694a48feaf89d.jpg', '$2y$10$k93d2r7Bn.xQhNyHOoKwku3TMPgjFMY30lbfyrBqcN0...', 'admin');

-- Seed flowers
INSERT INTO flowers (id, name, sku, category_id, price, stock, color, description, image, visible, keywords) VALUES
(1, 'Rose', 'ROSE-001', 4, 14.50, 30, 'Red', 'Classic romantic bloom with rich layered petals.', 'images/RoseLarge.webp', 1, 'rose,rosaceae,flower,romantic,thorn'),
(2, 'Cherry Blossom', 'CHBL-002', 4, 13.00, 28, NULL, 'Delicate spring blossoms with soft pink petals.', 'images/cherryblossom.jpg', 1, 'cherry blossom,sakura,rosaceae'),
(3, 'Apple Blossom', 'APBL-003', 4, 12.50, 24, NULL, 'Fragrant white-pink blooms from apple trees.', 'images/apple.jpg', 1, 'apple blossom,rosaceae'),
(4, 'Sunflower', 'SUN-004', 1, 11.50, 35, NULL, 'Bold golden petals with a striking dark center.', 'images/sunflower.jpg', 1, 'sunflower,helianthus,asteraceae'),
(5, 'Daisy', 'DAIS-005', 1, 10.50, 30, NULL, 'Simple cheerful flower with a bright yellow center...', 'images/daisy.jpg', 1, 'daisy,asteraceae'),
(6, 'Coneflower', 'CONE-006', 1, 12.00, 22, NULL, 'Hardy bloom with raised cone center and vibrant pe...', 'images/coneflower.webp', 1, 'coneflower,echinacea,asteraceae'),
(7, 'Lily', 'LILY-007', 6, 14.00, 27, NULL, 'Elegant trumpet-shaped flower with a sweet scent.', 'images/lily.jfif', 1, 'lily,liliaceae'),
(8, 'Tulip', 'TULIP-008', 6, 10.75, 36, NULL, 'Smooth cup-shaped blooms in vivid spring colors.', 'images/tulips.png', 1, 'tulip,liliaceae'),
(9, 'Peruvian Lily', 'PERU-009', 6, 13.50, 25, NULL, 'Striped petals and long-lasting cut flower favorit...', 'images/peruvianlily.jpg', 1, 'peruvian lily,alstroemeria,liliaceae'),
(10, 'Sweet Pea', 'SWEP-010', 3, 11.25, 21, NULL, 'Fragrant climbing bloom with soft ruffled petals.', 'images/sweetpea.jpg', 1, 'sweet pea,lathyrus,fabaceae'),
(11, 'Lupin', 'LUPN-011', 3, 12.75, 34, NULL, 'Tall spires of colorful pea-like flowers.', 'images/lupin.jpg', 1, 'lupin,lupinus,fabaceae'),
(12, 'Red Clover', 'CLOV-012', 3, 10.25, 29, NULL, 'Rounded pink-red flower heads on leafy stems.', 'images/redclover.webp', 1, 'red clover,trifolium,fabaceae'),
(13, 'Gladiolus', 'GLAD-013', 6, 13.25, 26, NULL, 'Tall spikes of bold, showy trumpet flowers.', 'images/gladiolus.jpg', 1, 'gladiolus'),
(14, 'Hydrangea', 'HYDR-014', 4, 14.25, 23, NULL, 'Large clustered blooms with lush ornamental appeal...', 'images/hydrangea.jpg!sw800', 1, 'hydrangea'),
(15, 'Marigold', 'MARI-015', 1, 10.95, 38, NULL, 'Bright golden-orange flower known for easy growth.', 'images/marigold.jpg', 1, 'marigold,tagetes,asteraceae'),
(17, 'Hibiscus', 'HIBS-017', 6, 15.60, 12, 'Pink', 'Large tropical flower, pink and yellow accents, gr...', 'images/hibiscus_pink_694942863d1e0.jpg', 1, 'pink, yellow, long stamper, tropical, large petals...');
