<?php
$pageTitle = 'Add Item - Campus Cart';
require_once __DIR__ . '/includes/header.php';

// check if logged in
if (!is_logged_in()) {
  redirect('login.php');
}

// form submission variables
$success = false;
$title = '';
$description = '';
$price = '';
$category = '';
$days_available = '';
$hours_available = '';
$image_path = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // take form submission inputs
    // reading and setting inputs
    $title = trim($_POST['title'] ?? '');
    $price_raw = trim($_POST['price'] ?? '');
    $price = is_numeric($price_raw) ? (float)$price_raw : -1;
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $days_raw = trim($_POST['available_days'] ?? '');
    $hours_raw = trim($_POST['available_hours'] ?? '');

    // image upload and save it in the uploades folder
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $filename = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); // extracting image name and image extension
        $destPath = __DIR__ . '/uploads/' . $filename; // create destination path
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {  // move the file to the uploads folder (upload file)
            $image_path = 'uploads/' . $filename; // setting image path to insert into database
        }
        
}
    // add item to the database with all variables
    $stmt = $conn->prepare(
      "INSERT INTO items (user_id,title,description,price,image_url,category,available_days,available_hours)
        VALUES (?,?,?,?,?,?,?,?)"
    );
    $uid = (int)$_SESSION['user_id']; // get user_id from session
    $stmt->bind_param("issdssss", $uid, $title, $description, $price, $image_path, $category, $days_raw, $hours_raw);
    if ($stmt->execute()) {
        $success = true;
        $title = $description = $price = $days_available = $hours_available = $category = $image_path = '';
    }
}
?>


<div class="row justify-content-center mt-4">
  <div class="col-md-8">
    <div class="card shadow">
      <div class="card-body">
        <h2 class="card-title mb-1">Add New Item</h2>
        <p class="text-muted mb-4">List an item for sale</p>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
          <?php if ($success): ?>
            <div class="alert alert-success mb-3">Item added! <a href="index.php">View marketplace</a></div>
          <?php else: ?>
            <div class="alert alert-danger mb-3">Failed to add item.</div>
          <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger mb-3">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label" for="title">Title *</label>
              <input class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="price">Price (৳) *</label>
              <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
            </div>
            <div class="col-md-12">
              <label class="form-label" for="description">Description *</label>
              <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="category">Category *</label>
              <select class="form-select" id="category" name="category" required>
                <option value="">Select Category</option>
                <?php foreach (get_allowed_categories() as $catName): ?>
                  <option value="<?php echo htmlspecialchars($catName); ?>" <?php echo $category === $catName ? 'selected':''; ?>>
                    <?php echo htmlspecialchars($catName); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="available_days">Available Days *</label>
              <input name="available_days" class="form-control" placeholder="Sunday,Monday" value="<?php echo htmlspecialchars($days_available); ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="available_hours">Available Hours *</label>
              <input name="available_hours" class="form-control" placeholder="11,12,13" value="<?php echo htmlspecialchars($hours_available); ?>" required>
            </div>
            <div class="col-md-12">
              <label class="form-label" for="image">Select Image *</label>
              <input class="form-control" type="file" id="image" name="image" accept="image/*" required>
            </div>
          </div>
          <div class="d-flex justify-content-between mt-3">
            <a class="btn btn-outline-secondary" href="index.php">Cancel</a>
            <button class="btn btn-primary" type="submit">Add Item</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>