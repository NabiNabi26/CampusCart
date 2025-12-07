<?php
$pageTitle = 'Transactions - Admin';
require_once __DIR__ . '/includes/header.php';
 
// Get transactions
$sql = "SELECT t.id, t.item_id, t.buyer_id, t.seller_id, t.amount,
               t.transaction_date, t.status,
               i.title,
               ub.name AS buyer_name,
               us.name AS seller_name
        FROM transactions t
        LEFT JOIN items i ON i.id = t.item_id
        LEFT JOIN users ub ON ub.id = t.buyer_id
        LEFT JOIN users us ON us.id = t.seller_id
        ORDER BY t.id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$transactions = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Transactions</h3>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Buyer</th>
            <th>Seller</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date & Time</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($t = $transactions->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$t['id']; ?></td>
              <td><?php echo htmlspecialchars($t['title'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($t['buyer_name'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($t['seller_name'] ?? ''); ?></td>
              <td>৳ <?php echo number_format((float)$t['amount'], 2); ?></td>
              <td><?php echo htmlspecialchars($t['status']); ?></td>
              <td><?php echo htmlspecialchars($t['transaction_date']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>