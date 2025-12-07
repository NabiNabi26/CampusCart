<?php
require_once __DIR__ . '/functions.php';

$currentScript = basename($_SERVER['PHP_SELF']);

function is_active(string $fileName): string {
    global $currentScript;
    return $currentScript === $fileName ? 'active' : '';
}

$isLoggedIn = is_logged_in();

if (is_admin()) {
    redirect('../Campus-Cart/admin/index.php');
}

$pageTitle = isset($pageTitle) ? $pageTitle : 'Campus Cart';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />      <!-- bootstrap link for styling -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />   <!-- font awesome link for icons -->
  <link href="assets/css/style.css" rel="stylesheet" />                                                       <!-- custom css for styling  -->
</head>
<body class="d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">
        <i class="fa-solid fa-cart-shopping text-primary"></i> Campus Cart
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?php echo is_active('index.php'); ?>" href="index.php">Home</a>
          </li>
          <?php if ($isLoggedIn): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo is_active('add_item.php'); ?>" href="add_item.php">Add Item</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo is_active('lending_requests.php'); ?>" href="lending_requests.php">Lending Requests</a>
            </li>
          <?php endif; ?>
        </ul>

        <ul class="navbar-nav ms-auto">
          <?php if ($isLoggedIn): ?>
            <li class="nav-item me-2">
              <a class="btn btn-outline-secondary" href="notifications.php">
                <i class="fa-regular fa-bell"></i>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="dashboard.php"><i class="fa-regular fa-id-card me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="saved_items.php"><i class="fa-solid fa-bookmark me-2"></i>Saved Items</a></li>
                <li><a class="dropdown-item" href="my_listings.php"><i class="fa-solid fa-list me-2"></i>My Listings</a></li>
                <li><a class="dropdown-item" href="my_history.php"><i class="fa-solid fa-history me-2"></i>My History</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item me-2">
              <a class="btn btn-outline-primary" href="login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-primary" href="register.php">Register</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <main class="flex-grow-1 py-4">
    <div class="container">