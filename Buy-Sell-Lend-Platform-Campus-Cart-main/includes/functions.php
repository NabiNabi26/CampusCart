<?php
session_start();

// database connection
require_once dirname(__DIR__) . '/dbconnect.php';

// check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

 // check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// function for redirecting to any page
function redirect($url) {
    header("Location: $url");
    exit();
}

// validate university email
function validate_university_email($email) {
    $university_domains ='g.bracu.ac.bd';
    $email_domain = strtolower(substr(strrchr($email, "@"), 1));
    return $email_domain === $university_domains;
}

// verify password
function verify_password($password, $stored) {
    return $password === $stored;
}

// get allowed categories
function get_allowed_categories(): array {
    return [
            'Electronics',
            'Books',
            'Clothing',
            'Sports',
            'Health & Beauty',
            'Toys & Games',
            'Arts & Crafts',
            'Computers',
            'Video Games',
            'Jewelry',
            'Pet Supplies',
            'Food & Beverages',
            'Stationery',
            'Transportation',
            'Musical Instruments',
            'Other'
    ];
}

// get filtered items list
function get_items($filters = [], string $orderBy, int $limit, int $offset) {
    global $conn;
    $where = [];
    $params = [];
    $types = '';

    if (!empty($filters['search'])) {  // match with searched string
        $where[] = 'i.title LIKE ?';
        $params[] = '%'.$filters['search'].'%';
        $types .= 's';
    }
    if (!empty($filters['category'])) {  // match with selected category
        $where[] = 'i.category = ?';
        $params[] = $filters['category'];
        $types .= 's';
    }

    $sql = "SELECT i.* FROM items i WHERE i.status = 'available'";
    if ($where) { // if filter used then where is not false and added where in sql
        $sql .= ' AND '.implode(' AND ', $where);
    }
    $sql .= " ORDER BY $orderBy LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

// toggle save and unsave items in one function
function toggle_save_item(int $user_id, int $item_id): bool {
    global $conn;
    $stmt = $conn->prepare("SELECT 1 FROM saved_items WHERE user_id=? AND item_id=?");  // if there is any item under the specific id
    $stmt->bind_param('ii', $user_id, $item_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    if ($exists) {
        $del = $conn->prepare("DELETE FROM saved_items WHERE user_id=? AND item_id=?");
        $del->bind_param('ii', $user_id, $item_id);
        return $del->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO saved_items (user_id,item_id) VALUES (?,?)");
        $ins->bind_param('ii', $user_id, $item_id);
        return $ins->execute();
    }
}

// check if item is saved
function is_item_saved(int $user_id, int $item_id): bool {
    global $conn;
    $stmt = $conn->prepare("SELECT 1 FROM saved_items WHERE user_id=? AND item_id=?");
    $stmt->bind_param('ii', $user_id, $item_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// get saved items  
function get_saved_items(int $user_id): array {
    global $conn;
    // join items table to get item details,  saved item id==item id for a specific user
    $sql = "SELECT i.*
            FROM saved_items s
            JOIN items i ON i.id = s.item_id
            WHERE s.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $items = [];
    while ($row = $res->fetch_assoc()) { $items[] = $row; } // fetch all saved items
    return $items;
}

// create lending requests
function create_lending_request(int $requester_id, string $item_name, string $description, string $needed_date, string $needed_time): bool {
    global $conn;
    $sql = "INSERT INTO lending_requests (requester_id, item_name, description, needed_date, needed_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issss', $requester_id, $item_name, $description, $needed_date, $needed_time);
    return $stmt->execute();
}

// get pending lending requests list
function list_pending_lending_requests(int $exclude_user_id = 0): array {
    global $conn;
    $sql = "SELECT lr.*, u.name AS requester_name
            FROM lending_requests lr
            JOIN users u ON lr.requester_id = u.id
            WHERE lr.status = 'pending'";
    if ($exclude_user_id > 0) { $sql .= " AND lr.requester_id <> " . (int)$exclude_user_id; }
    $sql .= " ORDER BY lr.id DESC";
    $res = $conn->query($sql);
    $rows = [];
    while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    return $rows;
}

// get my lending requests list
function list_my_lending_requests(int $user_id): array {
    global $conn;
    $sql = "SELECT * FROM lending_requests WHERE requester_id = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    return $rows;
}

// approve lending request
function approve_lending_request(int $request_id, int $approver_id): bool {
    global $conn;
    $stmt = $conn->prepare("UPDATE lending_requests 
                            SET status='approved', approved_by=?, approved_at=NOW() 
                            WHERE id=? AND status='pending'");
    $stmt->bind_param('ii', $approver_id, $request_id);
    return $stmt->execute() && $stmt->affected_rows > 0;
}

// get user by ID
function get_user_by_id($user_id) {
    global $conn;
    $user_id = (int)$user_id;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// get user rating
function get_user_average_rating(int $user_id): float {
    global $conn;
    $sql = "SELECT rating_avg FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return (float)$row['rating_avg'];
    }
    return 0.0;
}

// apply user rating
function apply_user_rating(int $rated_id, int $rating): bool {
    if ($rating < 1 || $rating > 5) return false;
    global $conn;
    $sql = "UPDATE users
            SET rating_avg = ((rating_avg * rating_count) + ?) / (rating_count + 1),
                rating_count = rating_count + 1
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('di', $rating, $rated_id);
    return $stmt->execute();
}

// get item by ID
function get_item_by_id(int $id): ?array {
    global $conn;
    $sql = "SELECT i.*, u.name AS seller_name
            FROM items i
            LEFT JOIN users u ON u.id = i.user_id
            WHERE i.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
}

// create notification
function create_notification(int $sender_id, int $receiver_id, string $message, string $type): bool {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, message, type, created_at) VALUES (?,?,?,?,NOW())");
    $stmt->bind_param('iiss', $sender_id, $receiver_id, $message, $type);
    return $stmt->execute();
}

// notifications
function get_notifications(int $user_id): array {
    global $conn;
    $sql = "SELECT n.*, s.name AS sender_name
            FROM notifications n
            LEFT JOIN users s ON n.sender_id = s.id
            WHERE n.receiver_id = ?
            ORDER BY n.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    return $rows;
}

// purchasing item and sending notification message
function purchase_item(int $item_id, int $buyer_id, float $amount, string $selected_hour, ?string $selected_day): bool {
    global $conn;
    $conn->begin_transaction();

    $stmt = $conn->prepare("SELECT user_id,status,price,title FROM items WHERE id=? FOR UPDATE");
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row || $row['status'] !== 'available' || (int)$row['user_id'] === $buyer_id) {
        $conn->rollback();
        return false;
    }

    $seller_id  = (int)$row['user_id'];
    $price      = (float)$row['price'];
    $item_title = $row['title'];

    $stmt2 = $conn->prepare("INSERT INTO transactions (item_id,buyer_id,seller_id,amount,selected_hour,selected_day,status,transaction_date)
                             VALUES (?,?,?,?,?,?, 'completed', NOW())");
    $stmt2->bind_param('iiidss', $item_id, $buyer_id, $seller_id, $price, $selected_hour, $selected_day);
    if (!$stmt2->execute()) { $conn->rollback(); return false; }

    $stmt3 = $conn->prepare("UPDATE items SET status='sold' WHERE id=?");
    $stmt3->bind_param('i', $item_id);
    if (!$stmt3->execute()) { $conn->rollback(); return false; }

    $conn->commit();

    // send notification
    $msg = "Your item (ID $item_id, \"$item_title\") was purchased. Meet at $selected_hour on $selected_day at pillar 1(Cafe).";
    create_notification($buyer_id, $seller_id, $msg, 'purchase');
    return true;
}

?>