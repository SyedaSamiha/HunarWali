CREATE TABLE IF NOT EXISTS sub_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
); 