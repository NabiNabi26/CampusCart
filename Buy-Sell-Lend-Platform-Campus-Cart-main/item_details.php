<?php
require_once __DIR__ . '/includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$itemId = (int)($_GET['id'] ?? 0); // get item ID from URL, default to 0 if not set 
$item = get_item_by_id($itemId); // fetch item details from database 
$userId = (int)$_SESSION['user_id']; // get logged-in user ID 
$message = ''; // message to show after purchase attempt

// strip hours from the items available hours(str) and appending it to a list 
$availableHourOptions = [];
$rawHours = array_filter(array_map('trim', explode(',', (string)$item['available_hours']))); 
foreach ($rawHours as $h) { 
    $availableHourOptions[] = $h;
}
$availableHourOptions = array_values(array_unique($availableHourOptions)); // remove duplicates and reindex
sort($availableHourOptions);

// strip days from the items available days(str) and appending it to a list 
$availableDayOptions = [];
$rawDays = array_filter(array_map('trim', explode(',', (string)$item['available_days'])));
foreach ($rawDays as $d) {
    $availableDayOptions[] = $d;
}
$availableDayOptions = array_values(array_unique($availableDayOptions));
sort($availableDayOptions);

// handle POST with selected_day and selected_hour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy'])) { // Check if form was submitted with the "buy" button
    $selected_day  = trim($_POST['selected_day'] ?? ''); // Get the selected day and hour from form
    $selected_hour = trim($_POST['selected_hour'] ?? '');

    // Pass both hour and day to purchase_item
    if (purchase_item($itemId, $userId, (float)$item['price'], $selected_hour, $selected_day)) {
        $message = 'Purchase successful.';
        $item = get_item_by_id($itemId);
        $chosenHour = $selected_hour; // If purchase succeeds: show message, refresh item data, store chosen hour
    } else {
      $message = 'Purchase not successful';
    }
}

$pageTitle = htmlspecialchars($item['title']).' - Item';
require_once __DIR__ . '/includes/header.php';
?>


<div class="row mt-4">
  <div class="col-md-5">
    <?php if (!empty($item['image_url'])): ?>
      <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="img-fluid rounded border" alt="<?php echo htmlspecialchars($item['title']); ?>">
    <?php endif; ?>
  </div>
  <div class="col-md-7">
    <h2 class="mb-1"><?php echo htmlspecialchars($item['title']); ?></h2>
    <div class="text-muted mb-2"><?php echo htmlspecialchars($item['category'] ?? 'Other'); ?></div>
    <div class="h4 mb-3">৳ <?php echo number_format((float)$item['price'], 2); ?></div>

    <?php if ($message): ?><div class="alert alert-success py-2"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

    <p class="mb-3" style="white-space:pre-line;"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>

    <p class="small text-muted mb-2">
      Days: <?php echo htmlspecialchars($item['available_days'] ?: 'N/A'); ?><br>
      Hours: <?php echo htmlspecialchars($item['available_hours'] ?: 'N/A'); ?>
    </p>

    <p class="small mb-3">Seller: <?php echo htmlspecialchars($item['seller_name'] ?? ''); ?></p>

    <?php if ($item['status'] === 'available' && $item['user_id'] != $userId): ?>
      <?php if (!empty($availableHourOptions)): ?>
        <form method="post" class="d-inline-flex align-items-end gap-2 flex-wrap">
          <div>
            <label class="form-label mb-1 small">Select Day *</label>
            <select name="selected_day" class="form-select form-select-sm" required style="min-width:140px">
              <option value="">--</option>
              <?php foreach ($availableDayOptions as $d): ?>
                <option value="<?php echo htmlspecialchars($d); ?>"><?php echo htmlspecialchars($d); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label mb-1 small">Select Hour *</label>
            <select name="selected_hour" class="form-select form-select-sm" required style="min-width:100px">
              <option value="">--</option>
              <?php foreach ($availableHourOptions as $h): ?>
                <option value="<?php echo htmlspecialchars($h); ?>"><?php echo htmlspecialchars($h); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="align-self-end">
            <button name="buy" value="1" class="btn btn-primary btn-sm">Buy Now</button>
          </div>
        </form>
      <?php endif; ?>
    <?php else: ?>
      <span class="badge bg-secondary"><?php echo htmlspecialchars(ucfirst($item['status'])); ?></span>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>