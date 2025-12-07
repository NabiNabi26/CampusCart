<?php
$pageTitle = 'Notifications - Campus Cart';
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
  redirect('login.php');
}

$user_id = (int)$_SESSION['user_id'];

// function for getting notifications
$notifications = get_notifications($user_id);
?>


<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 mb-0">Notifications</h1>
</div>

<?php if (empty($notifications)): ?>
  <div class="alert alert-warning">No notifications.</div>
<?php else: ?>
  <?php foreach ($notifications as $n): ?>
    <div class="border rounded p-3 mb-2">
      <div class="fw-bold text-muted small mb-1">
        <?php echo htmlspecialchars($n['type']); ?>
        <?php if (!empty($n['created_at'])): ?> • <?php echo htmlspecialchars($n['created_at']); ?><?php endif; ?>
      </div>
      <div><?php echo nl2br(htmlspecialchars($n['message'])); ?></div>
      <?php if (!empty($n['sender_name'])): ?>
        <div class="small text-muted mt-1">From: <?php echo htmlspecialchars($n['sender_name']); ?></div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
