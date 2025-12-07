<?php
$pageTitle = 'Lending Requests - Campus Cart';
require_once __DIR__ . '/includes/header.php';

// check if user is logged in
if (!is_logged_in()) {
  redirect('login.php');
}

$success = false;
$user_id = (int)$_SESSION['user_id']; // get user_id from session

// create request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {  // form submission input
  $item_name = trim($_POST['item_name'] ?? ''); // reading
  $description = trim($_POST['description'] ?? '');
  $needed_date = $_POST['needed_date'] ?? '';
  $needed_time = $_POST['needed_time'] ?? '';

  // insert into database
  if (create_lending_request($user_id, $item_name, $description, $needed_date, $needed_time)) {
    $success = true;
  } else {
    $success = false;
  }

}

// approve request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'approve') {
  $request_id = (int)($_POST['request_id'] ?? 0);
  if (approve_lending_request($request_id, $user_id)) {

    // php codes for getting the requester_id and item_name from the database
    $rs = $conn->prepare("SELECT requester_id, item_name FROM lending_requests WHERE id=?");
    $rs->bind_param('i', $request_id);
    $rs->execute();
    $reqRow = $rs->get_result()->fetch_assoc();

    // creating approval notification on database
    if ($reqRow) {
      $msg = "Your lending request ('".$reqRow['item_name']."') has been approved. Meet the lendee on Pillar 1 (Cafeteria)";
      create_notification($user_id, (int)$reqRow['requester_id'], $msg, 'approval');
    }
    $success = true;
  } else {
    $success = false;
  }
}

// for showing my lending requests, insert data into lending_requests table
$my_requests = list_my_lending_requests($user_id);
$pending_requests = list_pending_lending_requests($user_id);
?>

<div class="row g-3">
  <div class="col-md-5">
    <div class="card shadow">
      <div class="card-body">
        <h5 class="card-title mb-3">Create Lending Request</h5>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'create')): ?>
          <?php if ($success): ?>
            <div class="alert alert-success mb-3">Request created.</div>
          <?php else: ?>
            <div class="alert alert-danger mb-3">Failed to create request.</div>
          <?php endif; ?>
        <?php endif; ?>

        <form method="post">
          <input type="hidden" name="action" value="create">
          <div class="mb-3">
            <label class="form-label" for="item_name">Item Name *</label>
            <input class="form-control" id="item_name" name="item_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="description">Description *</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label" for="needed_date">Date Needed *</label>
            <input type="date" class="form-control" id="needed_date" name="needed_date" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="needed_time">Time Needed *</label>
            <input type="time" class="form-control" id="needed_time" name="needed_time" required>
          </div>
          <div class="d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">Create Request</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card shadow mb-3">
      <div class="card-body">
        <h5 class="card-title mb-3">Pending Requests (Others)</h5>
        <?php if (empty($pending_requests)): ?>
          <div class="alert alert-warning">No pending requests from others.</div>
        <?php else: ?>
          <?php foreach ($pending_requests as $req): ?>
            <div class="border rounded p-3 mb-2">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold"><?php echo htmlspecialchars($req['item_name']); ?></div>
                  <div class="small text-muted">Requested by: <?php echo htmlspecialchars($req['requester_name']); ?></div>
                  <div class="small text-muted">Needed: <?php echo htmlspecialchars($req['needed_date']); ?> at <?php echo htmlspecialchars($req['needed_time']); ?></div>
                  <?php if (!empty($req['description'])): ?>
                  <div class="mt-1"><?php echo nl2br(htmlspecialchars($req['description'])); ?></div>
                  <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                  <form method="post">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="request_id" value="<?php echo (int)$req['id']; ?>">
                    <button class="btn btn-primary btn-sm" type="submit">Approve</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="card shadow">
      <div class="card-body">
        <h5 class="card-title mb-3">My Requests</h5>
        <?php if (empty($my_requests)): ?>
          <div class="alert alert-warning">You haven't created any requests yet.</div>
        <?php else: ?>
          <?php foreach ($my_requests as $req): ?>
            <div class="border rounded p-3 mb-2">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-bold"><?php echo htmlspecialchars($req['item_name']); ?></div>
                  <div class="small text-muted">Status: <?php echo htmlspecialchars($req['status']); ?></div>
                  <div class="small text-muted">Needed: <?php echo htmlspecialchars($req['needed_date']); ?> at <?php echo htmlspecialchars($req['needed_time']); ?></div>
                  <?php if (!empty($req['description'])): ?>
                  <div class="mt-1"><?php echo nl2br(htmlspecialchars($req['description'])); ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>