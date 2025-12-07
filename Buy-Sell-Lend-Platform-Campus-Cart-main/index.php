<?php
require_once __DIR__ . '/includes/functions.php'; //Loads the functions file from the includes directory

// check if logged in
if (!is_logged_in()) {
  redirect('login.php');
}

// save/unsave toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_save'], $_POST['item_id'])) { 
  //if form was submitted AND both toggle_save and item_id fields exist
    toggle_save_item((int)$_SESSION['user_id'], (int)$_POST['item_id']); // toggle save item for the logged-in user
    header('Location: index.php'); // redirect back to the index page
    exit; 
}

$pageTitle = 'Home - Campus Cart';
require_once __DIR__ . '/includes/header.php';

// take filters
$search    = trim($_GET['search'] ?? ''); // get search term input
$category  = trim($_GET['category'] ?? ''); // get category input

$allowedCats = get_allowed_categories(); // get list of allowed categories from the database

// filters
$filters = [];
if ($search !== '')   $filters['search']   = $search; // if search term is not empty do this
if ($category !== '') $filters['category'] = $category; //if category is not empty do this

// show all items randomly
$items = get_items($filters, 'RAND()', 1000000, 0); // get items based on filters, random order, no limit
$userId = (int)$_SESSION['user_id']; // get logged-in user ID
?>


<form class="row g-2 mb-3" method="get">
  <div class="col-sm-6 col-md-4">
    <input class="form-control" name="search" placeholder="Search items..." value="<?php echo htmlspecialchars($search); ?>">
  </div>
  <div class="col-sm-4 col-md-3">
    <select name="category" class="form-select">
      <option value="">All Categories</option>
      <?php foreach ($allowedCats as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $category === $c ? 'selected':''; ?>>
          <?php echo htmlspecialchars($c); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-sm-2 col-md-2">
    <button class="btn btn-outline-secondary w-100">Filter</button>
  </div>
</form>

<?php if (empty($items)): ?>
  <div class="alert alert-info">No items found.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($items as $item):
      $saved = is_item_saved($userId, (int)$item['id']);
    ?>
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100">
          <?php if (!empty($item['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="badge bg-secondary"><?php echo htmlspecialchars($item['category'] ?? 'Other'); ?></span>
              <form method="post" class="m-0">
                <input type="hidden" name="item_id" value="<?php echo (int)$item['id']; ?>">
                <button name="toggle_save" class="btn btn-sm <?php echo $saved?'btn-success':'btn-outline-secondary'; ?>">
                  <?php echo $saved ? 'Saved' : 'Save'; ?>
                </button>
              </form>
            </div>
            <h5 class="card-title mb-1 text-truncate"><?php echo htmlspecialchars($item['title']); ?></h5>
            <div class="small text-muted mb-2">
              <?php
                $desc = trim($item['description'] ?? '');
                if ($desc !== '') {
                  $snip = mb_substr($desc, 0, 70);
                  if (mb_strlen($desc) > 70) $snip .= '...';
                  echo htmlspecialchars($snip);
                }
              ?>
            </div>
            <div class="fw-bold mb-3">৳ <?php echo number_format((float)$item['price'], 2); ?></div>
            <a href="item_details.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-sm btn-outline-primary mt-auto">View</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>