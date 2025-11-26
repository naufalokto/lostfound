-- Category Table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name) VALUES 
('Electronics') ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name) VALUES 
('Documents') ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name) VALUES 
('Wallet') ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name) VALUES 
('Keys') ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name) VALUES 
('Clothing') ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name) VALUES 
('Accessories') ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name) VALUES 
('Books') ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name) VALUES 
('Others') ON DUPLICATE KEY UPDATE name=VALUES(name);

