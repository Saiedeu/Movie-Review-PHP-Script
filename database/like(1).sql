-- Add spoiler field to reviews table
ALTER TABLE reviews ADD COLUMN spoiler_content TEXT AFTER content;

-- Create comments table
CREATE TABLE review_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    parent_id INT NULL,
    commenter_name VARCHAR(100) NOT NULL,
    commenter_email VARCHAR(150) NOT NULL,
    comment_text TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_review_id (review_id),
    INDEX idx_parent_id (parent_id),
    INDEX idx_status (status),
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES review_comments(id) ON DELETE CASCADE
);

-- Create likes table
CREATE TABLE review_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT NOT NULL,
    user_ip VARCHAR(45) NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (review_id, user_ip),
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE
);