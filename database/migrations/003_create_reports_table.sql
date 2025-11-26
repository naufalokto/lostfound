-- Report Table (Lost/Found Items)
CREATE TABLE IF NOT EXISTS reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('LOST', 'FOUND') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT,
    location VARCHAR(255) NOT NULL,
    image_path VARCHAR(255),
    status ENUM('ACTIVE', 'SOLVED', 'ARCHIVED') DEFAULT 'ACTIVE',
    verification_question VARCHAR(255),
    verification_answer VARCHAR(255),
    phone_hidden BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create indexes for performance
CREATE INDEX idx_user_id ON reports(user_id);
CREATE INDEX idx_category_id ON reports(category_id);
CREATE INDEX idx_type_status ON reports(type, status);

