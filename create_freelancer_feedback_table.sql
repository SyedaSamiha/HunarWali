CREATE TABLE IF NOT EXISTS freelancer_feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    freelancer_id INT NOT NULL,
    client_id INT NOT NULL,
    overall_rating DECIMAL(2,1) NOT NULL,
    communication_rating INT NOT NULL,
    payment_rating INT NOT NULL,
    feedback_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (freelancer_id) REFERENCES users(id),
    FOREIGN KEY (client_id) REFERENCES users(id),
    UNIQUE KEY unique_freelancer_feedback (order_id, freelancer_id)
); 