<?php
$pageTitle = 'Dashboard - Campus Cart';
require_once __DIR__ . '/includes/header.php';

// check if logged in
if (!is_logged_in()) {
  redirect('login.php');
}

$userId     = (int)$_SESSION['user_id'];
$user       = get_user_by_id($userId);
$userRating = get_user_average_rating($userId);
?>


<div class="row g-3">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow">
      <div class="card-body">
        <h5 class="card-title mb-3">My Profile</h5>
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input class="form-control" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" disabled>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" disabled>
        </div>
        <div class="mb-3">
          <label class="form-label">User Rating</label>
          <input class="form-control" value="<?php echo number_format($userRating, 2); ?> / 5" disabled>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
