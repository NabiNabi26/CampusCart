<?php
$pageTitle = 'Admin Dashboard - Campus Cart';
require_once __DIR__ . '/includes/header.php';
?>

<h3 class="mb-4">Admin Dashboard</h3>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <i class="fa-solid fa-users fa-2x text-primary mb-2"></i>
        <div class="h4 mb-0"><?php echo number_format(admin_count_users())-1; ?></div>
        <div class="text-muted">Total Users</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <i class="fa-solid fa-box fa-2x text-success mb-2"></i>
        <div class="h4 mb-0"><?php echo number_format(admin_count_available_items());?> / <?php echo number_format(admin_count_sold_items());?></div>
        <div class="text-muted">Available Items / Sold Items</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <i class="fa-solid fa-handshake fa-2x text-info mb-2"></i>
        <div class="h4 mb-0"><?php echo number_format(admin_count_transactions()); ?></div>
        <div class="text-muted">Transactions</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm">
      <div class="card-body text-center">
        <i class="fa-solid fa-clock fa-2x text-warning mb-2"></i>
        <div class="h4 mb-0"><?php echo number_format(admin_count_pending_lending()); ?> / <?php echo number_format(admin_count_approved_lending()); ?></div>
        <div class="text-muted">Pending Lending / Approved Lending</div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>