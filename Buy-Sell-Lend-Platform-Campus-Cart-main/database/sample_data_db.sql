-- Insert admin user (password: CampusCartAdmin)
INSERT INTO users (name, email, password, role) VALUES
('Campus Cart Admin', 'campus.cart.admin@g.bracu.ac.bd', 'CampusCartAdmin', 'admin');

-- Insert sample regular user
INSERT INTO users (name, email, password, role) VALUES
('Nabila Mubasshira', 'nabila@g.bracu.ac.bd', 'nabila26', 'user'),
('Masrafi Islam', 'masrafi@g.bracu.ac.bd', 'masrafi19', 'user'),
('Mahathir Alam', 'mahathir@g.bracu.ac.bd', 'mahathir', 'user'),
('Arunima Das', 'arunima@g.bracu.ac.bd', 'arunima', 'user'),
('Syed Tanmoy', 'tanmoy@g.bracu.ac.bd', 'tanmoy', 'user'),
('Nabil Abdullah', 'nabil@g.bracu.ac.bd', 'nabil', 'user'),
('Maisha Abdullah', 'maisha@g.bracu.ac.bd', 'maisha', 'user'),
('Hasin Arman', 'hasin@g.bracu.ac.bd', 'hasin', 'user'),
('Sadiba Tazalli', 'sadiba@g.bracu.ac.bd', 'sadiba', 'user'),
('Faiza Sarah', 'faiza@g.bracu.ac.bd', 'faiza', 'user'),
('Tasmia Rahman', 'tasmia@g.bracu.ac.bd', 'tasmia', 'user'),
('Mahin Khan', 'mahin@g.bracu.ac.bd', 'mahin', 'user'),
('Nafisa Neha', 'nafisa@g.bracu.ac.bd', 'nafisa', 'user'),
('Adib Islam', 'adib@g.bracu.ac.bd', 'adib', 'user'),
('Adiba Tasnim', 'adiba@g.bracu.ac.bd', 'adiba', 'user');

-- Insert item data
INSERT INTO items (user_id, title, description, price, image_url, category, available_days, available_hours, status) VALUES
(2, 'Wireless Bluetooth Earbuds', 'High-quality wireless earbuds with noise cancellation (New)', 2400, 'uploads/earbuds.png', 'Electronics', 'Monday, Tuesday, Wednesday, Thursday, Friday', '10,11', 'available'),
(2, 'Classic Leather-Bound Notebook', 'Premium leather notebook with 200 lined pages (Good)', 2450, 'uploads/notebook.png', 'Books', 'Monday, Wednesday, Friday', '10,14', 'available'),
(3, 'Keyboard', 'WK68 Weikav Mechanical Gaming Keyboard Purple (New)', 4000, 'uploads/wk68.jpg', 'Electronics', 'Monday, Wednesday', '10,11', 'available'),
(4, 'Basketball', 'Durable rubber basketball for indoor/outdoor use, official Size 7 (New)', 2999, 'uploads/basketball.png', 'Sports', 'Monday, Wednesday, Friday, Sunday', '08,12', 'available'),
(5, 'Organic Lavender Body Lotion', 'Soothing moisturizer made with natural ingredients (New)', 1299, 'uploads/lotion.png', 'Health & Beauty', 'Everyday', '09,17', 'available'),
(6, 'Board Game: Strategy Edition', 'Family-friendly board game for 2-6 players (Good)', 1999, 'uploads/boardgame.png', 'Toys & Games', 'Saturday, Sunday', '10,13', 'available'),
(7, 'Acrylic Paint Set (24 Colors)', 'Vibrant acrylic paints with brushes included (New)', 1875, 'uploads/paintset.png', 'Arts & Crafts', 'Tuesday, Thursday, Saturday', '11,14', 'available'),
(8, 'Refurbished Laptop (i5, 8GB RAM)', 'Reliable laptop for work/school, 256GB SSD (Good)', 24999, 'uploads/laptop.png', 'Computers', 'Monday, Wednesday, Friday', '10,14', 'available'),
(9, 'PlayStation 5', 'Popular action-adventure game, complete with case (Good)', 75000, 'uploads/ps5.png', 'Video Games', 'Friday, Saturday', '12,15', 'available'),
(10, 'Sterling Silver Necklace', 'Elegant 18" chain with pendant (Poor)', 1550, 'uploads/necklace.png', 'Jewelry', 'Wednesday, Sunday', '10,13', 'available'),
(11, 'Dog Chew Toy', 'Durable rubber toys for medium/large dogs (New)', 999, 'uploads/dogtoy.png', 'Pet Supplies', 'Everyday', '08,20', 'available'),
(12, 'Gourmet Coffee Sampler', '5-pack of premium coffee blends (New)', 1495, 'uploads/coffee.png', 'Food & Beverages', 'Monday, Thursday, Saturday', '09,12', 'available'),
(13, 'Planner with Stickers', '2024 weekly planner with decorative stickers (Good)', 825, 'uploads/planner.png', 'Stationery', 'Tuesday, Friday', '11,14', 'available'),
(14, 'Bicycle Repair Kit', 'Essential tools for bike maintenance (New)', 2240, 'uploads/bikekit.png', 'Transportation', 'Wednesday, Saturday', '10,13', 'available'),
(15, 'Used Acoustic Guitar', 'Beginner-friendly guitar with soft case (Poor)', 3500, 'uploads/guitar.png', 'Musical Instruments', 'Sunday', '12,18', 'available'),
(16, 'Men''s Winter Jacket', 'Waterproof insulated jacket for cold weather, size L (Good)', 4500, 'uploads/jacket.png', 'Clothing', 'Tuesday, Thursday, Saturday', '11,15', 'available');

-- Insert lending request data
INSERT INTO lending_requests (requester_id, item_name, description, needed_date, needed_time) VALUES
(3,'Calculator','Casio Classwiz fx-991CW','2025-08-19','15:30:00'),
(11,'Scientific calculator','Casio Classwiz fx-991CW','2025-09-03','09:30:00'),
(11,'Webcam','for online exam/meeting','2025-09-05','08:00:00'),
(12,'Charger','MacBook Pro Charger (87W USB-C)','2025-09-15','16:00:00'),
(12,'Scientific calculator','Casio Classwiz fx-991','2025-09-13','09:30:00'),
(12,'Umbrella','suitable for couple','2025-09-27','12:00:00'),
(13,'Headset','Logitech H390 Noise-Canceling Headset','2025-09-08','14:15:00'),
(14,'Mic','Blue Snowball USB Mic','2025-09-22','10:45:00'),
(15,'Coursebook','ENG101','2025-09-11','13:00:00'),
(15,'Headset','Logitech H390 Noise-Canceling Headset','2025-09-20','14:15:00'),
(15,'Powerbank','can charge my phone to at least 20%','2025-09-19','18:30:00'),
(16,'Powerbank','can charge my phone to at least 20%','2025-09-15','18:30:00');