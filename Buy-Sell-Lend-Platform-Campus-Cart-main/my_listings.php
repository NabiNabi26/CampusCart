<?php
$pageTitle = 'My Listings - Campus Cart';
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
  redirect('login.php');
}
// get user id
$user_id = (int)$_SESSION['user_id'];

// for showing all listed items
$sql = "SELECT id, title, price, image_url, category, available_days, available_hours, created_at
        FROM items
        WHERE user_id = ?
        ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$listings = [];
while ($row = $res->fetch_assoc()) {
  $listings[] = $row;
}
?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 mb-0">My Listings</h1>
</div>

<?php if (empty($listings)): ?>
  <div class="alert alert-info">You have no listings yet.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($listings as $item): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <?php if (!empty($item['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between mb-2">
              <span class="badge bg-secondary text-uppercase"><?php echo 'sell'; ?></span>
              <span class="small text-muted"><?php echo htmlspecialchars($item['category'] ?? 'Other'); ?></span>
            </div>
            <h5 class="card-title mb-1 text-truncate"><?php echo htmlspecialchars($item['title']); ?></h5>
            <div class="fw-bold mb-2">৳ <?php echo number_format((float)$item['price'], 2); ?></div>
            <div class="small text-muted mb-2">
              Days: <?php echo htmlspecialchars($item['available_days'] ?: 'N/A'); ?><br>
              Hours: <?php echo htmlspecialchars($item['available_hours'] ?: 'N/A'); ?>
            </div>
            <div class="mt-auto d-flex gap-2">
              <a href="item_details.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>