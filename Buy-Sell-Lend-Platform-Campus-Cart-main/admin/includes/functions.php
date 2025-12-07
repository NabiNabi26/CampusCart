<?php
require_once __DIR__ . '/../../includes/functions.php';

// admin functions

// check if user is admin
function require_admin(): void {
    if (!is_logged_in() || !is_admin()) {
        redirect('../login.php');
    }
}

// count users
function admin_count_users(): int {
    global $conn;
    $res = $conn->query("SELECT COUNT(*) c FROM users");
    return (int)($res->fetch_assoc()['c'] ?? 0);
}

// count available items
function admin_count_available_items(): int {
    global $conn;
    $res = $conn->query("SELECT COUNT(*) c FROM items WHERE status='available'");
    return (int)($res->fetch_assoc()['c'] ?? 0);
}

// count sold items
function admin_count_sold_items(): int {
    global $conn;
    $res = $conn->query("SELECT COUNT(*) c FROM items WHERE status='sold'");
    return (int)($res->fetch_assoc()['c'] ?? 0);
}

// count transactions
function admin_count_transactions(): int {
    global $conn;
    $res = $conn->query("SELECT COUNT(*) c FROM transactions");
    return (int)($res->fetch_assoc()['c'] ?? 0);
}

// count pending lending requests
function admin_count_pending_lending(): int {
    global $conn;
    $res = $conn->query("SELECT COUNT(*) c FROM lending_requests WHERE status='pending'");
    return (int)($res->fetch_assoc()['c'] ?? 0);
}

// count approved lending requests
function admin_count_approved_lending(): int {
    global $conn;
    $res = $conn->query("SELECT COUNT(*) c FROM lending_requests WHERE status='approved'");
    return (int)($res->fetch_assoc()['c'] ?? 0);
}
?>