<?php
$pageTitle = 'Lending Requests - Admin';
require_once __DIR__ . '/includes/header.php';

// Get lending requests
$sql = "SELECT lr.*, u.name AS requester_name
        FROM lending_requests lr
        JOIN users u ON u.id = lr.requester_id"; // JOIN lending requests to their requesters

$params = [];
$types = '';

$sql .= " ORDER BY lr.id ASC";
$stmt = $conn->prepare($sql);
if ($params) {
  $stmt->bind_param($types, ...$params); 
}
$stmt->execute();
$requests = $stmt->get_result();// $requests now contains all the lending request records from the database
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Lending Requests</h3>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Requester</th>
            <th>Description</th>
            <th>Status</th>
            <th>Requested Date & Time</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($request = $requests->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$request['id']; ?></td>
              <td><?php echo htmlspecialchars($request['item_name']); ?></td>
              <td><?php echo htmlspecialchars($request['requester_name']); ?></td>
              <td><?php echo htmlspecialchars($request['description'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($request['status'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($request['requested_at']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
