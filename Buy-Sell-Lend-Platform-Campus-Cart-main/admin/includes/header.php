<?php
require_once __DIR__ . '/functions.php';
require_admin();

$currentScript = basename($_SERVER['PHP_SELF']);
function admin_is_active(string $file): string {
    global $currentScript;
    return $currentScript === $file ? 'active' : '';
}

$pageTitle = isset($pageTitle) ? $pageTitle : 'Admin - Campus Cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="index.php"><i class="fa-solid fa-gauge-high"></i> Admin Panel</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="adminNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link <?php echo admin_is_active('index.php'); ?>" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?php echo admin_is_active('users.php'); ?>" href="users.php">Users</a></li>
          <li class="nav-item"><a class="nav-link <?php echo admin_is_active('items.php'); ?>" href="items.php">Items</a></li>
          <li class="nav-item"><a class="nav-link <?php echo admin_is_active('transactions.php'); ?>" href="transactions.php">Transactions</a></li>
          <li class="nav-item"><a class="nav-link <?php echo admin_is_active('lending_requests.php'); ?>" href="lending_requests.php">Lendings</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link text-warning" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="flex-grow-1 py-4">
    <div class="container">
