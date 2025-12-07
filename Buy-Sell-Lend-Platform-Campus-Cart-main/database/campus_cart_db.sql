-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(20) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  rating_avg DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  rating_count INT NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY email (email)
);

-- Items
CREATE TABLE items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(50) NOT NULL,
  description TEXT DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL,
  status ENUM('available','sold') DEFAULT 'available',
  image_url VARCHAR(50) DEFAULT NULL,
  category VARCHAR(50) NOT NULL DEFAULT 'Other',
  available_days VARCHAR(60) DEFAULT NULL,
  available_hours VARCHAR(50) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT items_fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Transactions
CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  buyer_id INT NOT NULL,
  seller_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  buyer_rating TINYINT NULL,
  seller_rating TINYINT NULL,
  selected_hour VARCHAR(3) NULL,
  selected_day VARCHAR(10) NULL,
  transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','completed') DEFAULT 'pending',
  CONSTRAINT transactions_fk_item_id FOREIGN KEY (item_id) REFERENCES items(id),
  CONSTRAINT transactions_fk_buyer_id FOREIGN KEY (buyer_id) REFERENCES users(id),
  CONSTRAINT transactions_fk_seller_id FOREIGN KEY (seller_id) REFERENCES users(id)
);

-- Lending Requests
CREATE TABLE lending_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  requester_id INT NOT NULL,
  item_name VARCHAR(100) NOT NULL,
  description TEXT DEFAULT NULL,
  needed_date DATE DEFAULT NULL,
  needed_time TIME DEFAULT NULL,
  status ENUM('pending','approved') DEFAULT 'pending',
  requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  approved_at DATETIME DEFAULT NULL,
  approved_by INT DEFAULT NULL,
  CONSTRAINT lending_requests_fk_requester_id FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT lending_requests_fk_approved_by FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Saved Items
CREATE TABLE saved_items (
  user_id INT NOT NULL,
  item_id INT NOT NULL,
  added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, item_id),
  CONSTRAINT saved_items_fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT saved_items_fk_item_id FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- Notifications
CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  message TEXT NOT NULL,
  type ENUM('purchase','approval'),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT notifications_fk_sender_id FOREIGN KEY (sender_id) REFERENCES users(id),
  CONSTRAINT notifications_fk_receiver_id FOREIGN KEY (receiver_id) REFERENCES users(id)
);