<?php
$pageTitle = 'Manage Items - Admin';
require_once __DIR__ . '/includes/header.php';

// Get items with filters
$search        = trim($_GET['search'] ?? '');
$statusFilter  = $_GET['status'] ?? '';
$categoryFilter= trim($_GET['category'] ?? ''); // get search ,status ,category from URL, default to empty string if not set

$sql = "SELECT i.*, u.name AS seller_name
        FROM items i 
        LEFT JOIN users u ON u.id = i.user_id  
        WHERE 1=1"; // JOIN items to their sellers
$params = [];
$types  = ''; // Empty string for parameter types (s=string, i=integer, etc.)

if ($search !== '') { 
  $sql .= " AND (i.title LIKE ? OR u.name LIKE ?)"; // search in title or seller name
  $term = "%$search%"; 
  $params[] = $term; $params[] = $term;
  $types .= 'ss';
}

if ($statusFilter !== '' && in_array($statusFilter, ['available','sold'], true)) {
  $sql .= " AND i.status = ?"; // filter by status if valid
  $params[] = $statusFilter; 
  $types .= 's';
}

if ($categoryFilter !== '') {
  $sql .= " AND i.category = ?"; // filter by category
  $params[] = $categoryFilter;
  $types .= 's';
}

$sql .= " ORDER BY i.id ASC"; // Orders results by item ID in ascending order
$stmt = $conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$items = $stmt->get_result(); // $items now contains all the item records from the database

$allowedCategories = get_allowed_categories();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Manage Items</h3>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-md-4">
    <input class="form-control" name="search" placeholder="Search title or seller..." value="<?php echo htmlspecialchars($search); ?>">
  </div>
  <div class="col-md-2">
    <select class="form-select" name="category">
      <option value="">All Categories</option>
      <?php foreach ($allowedCategories as $cat): ?>
        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $categoryFilter === $cat ? 'selected':''; ?>>
          <?php echo htmlspecialchars($cat); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <select class="form-select" name="status">
      <option value="">All Status</option>
      <option value="available" <?php echo $statusFilter==='available'?'selected':''; ?>>Available</option>
      <option value="sold" <?php echo $statusFilter==='sold'?'selected':''; ?>>Sold</option>
    </select>
  </div>
  <div class="col-md-2">
    <button class="btn btn-outline-secondary w-100">Filter</button>
  </div>
</form>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Seller</th>
            <th>Category</th>
            <th>Status</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($item = $items->fetch_assoc()): ?>
            <tr>
              <td><?php echo (int)$item['id']; ?></td>
              <td><?php echo htmlspecialchars($item['title']); ?></td>
              <td><?php echo htmlspecialchars($item['seller_name'] ?? ''); ?></td>
              <td><?php echo htmlspecialchars($item['category'] ?? 'Other'); ?></td>
              <td><?php echo htmlspecialchars($item['status']); ?></td>
              <td>৳ <?php echo number_format((float)$item['price'], 2); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
