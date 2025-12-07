<?php
$pageTitle = 'Saved Items - Campus Cart';
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
  redirect('login.php');
}

// this unsaves an item from the saved items by using toggle function
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsave_id'])) {
  $item_id = (int)$_POST['unsave_id'];
  toggle_save_item((int)$_SESSION['user_id'], $item_id);
  header('Location: saved_items.php');
  exit;
}
// get saved items
$saved = get_saved_items((int)$_SESSION['user_id']);


?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 mb-0">Saved Items</h1>
</div>

<?php if (empty($saved)): ?>
  <div class="alert alert-warning">You have no saved items.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($saved as $item): ?>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100">
          <?php if (!empty($item['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($item['listing_type'] ?? 'sell'); ?></span>
              <form method="post" class="m-0">
                <input type="hidden" name="unsave_id" value="<?php echo (int)$item['id']; ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Unsave</button>
              </form>
            </div>
            <h5 class="card-title mb-1 text-truncate"><?php echo htmlspecialchars($item['title']); ?></h5>
            <div class="small text-muted mb-2"><?php echo htmlspecialchars($item['category'] ?? 'Other'); ?></div>
            <div class="fw-bold mb-3">৳ <?php echo number_format((float)$item['price'], 2); ?></div>
            <a href="item_details.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-outline-primary mt-auto">View</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
