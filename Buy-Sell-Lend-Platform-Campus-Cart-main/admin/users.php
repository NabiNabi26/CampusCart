<?php
$pageTitle = 'Manage Users - Admin'; // Page title for the admin users page 
require_once __DIR__ . '/includes/header.php';  // Loads the HTML header template file

$sql = "SELECT id, name, email, role, rating_avg, created_at FROM users"; // SQL query to select user details 
$params = []; // Creates empty array for query parameters
$types = ''; // Creates empty string for parameter types

$sql .= " ORDER BY id ASC"; // Orders results by user ID in ascending order
$stmt = $conn->prepare($sql); 
if ($params) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$users = $stmt->get_result(); //// $users now contains all the user records from the database
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Manage Users</h3>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Rating</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($user = $users->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$user['id']; ?></td>
              <td><?php echo htmlspecialchars($user['name']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo htmlspecialchars($user['role']); ?></td>
              <td><?php echo htmlspecialchars($user['rating_avg']); ?></td>
              <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>