-- ShopNex Database Schema
CREATE DATABASE IF NOT EXISTS shopnex_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopnex_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    image_url VARCHAR(500),
    stock INT DEFAULT 100,
    reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_intent_id VARCHAR(255),
    payment_status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO products (name, description, price, category, image_url, reviews) VALUES
('Minimalist Watch', 'A sleek, minimalist timepiece with Japanese quartz movement. Water-resistant to 30m. Perfect for both casual and formal occasions.', 129.99, 'accessories', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&q=80', 128),
('Wireless Earbuds Pro', 'True wireless earbuds with active noise cancellation, 24-hour battery life, and premium audio drivers. IPX4 water resistance.', 89.99, 'electronics', 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600&q=80', 245),
('Leather Notebook', 'Handcrafted genuine leather journal with 200 acid-free pages. Lay-flat binding and ribbon bookmark.', 34.99, 'lifestyle', 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=600&q=80', 89),
('Ceramic Coffee Mug', 'Hand-thrown ceramic mug with a matte glaze finish. 350ml capacity, dishwasher safe.', 24.99, 'lifestyle', 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?w=600&q=80', 62),
('Portable Charger 20K', '20,000mAh power bank with 65W USB-C fast charging. Charge your laptop, phone, and tablet simultaneously.', 49.99, 'electronics', 'https://images.unsplash.com/photo-1618433048900-9e2e8ede5e2e?w=600&q=80', 311),
('Canvas Tote Bag', 'Heavy-duty 100% organic cotton canvas tote. Reinforced stitching and long handles. Perfect for everyday carry.', 19.99, 'accessories', 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=600&q=80', 74);
