<?php
$pageTitle = 'My Transaction History - Campus Cart';
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in()) {
  redirect('login.php');
}

$user_id = (int)$_SESSION['user_id'];

// input one-time buyer_rating and seller_rating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'], $_POST['rating'])) {
    $transaction_id = (int)$_POST['transaction_id'];
    $rating         = (int)$_POST['rating'];

    // get transaction to determine user roles
    $txStmt = $conn->prepare("SELECT id, buyer_id, seller_id, buyer_rating, seller_rating FROM transactions WHERE id=? AND (buyer_id=? OR seller_id=?)");
    $txStmt->bind_param('iii', $transaction_id, $user_id, $user_id);
    $txStmt->execute();
    $tx = $txStmt->get_result()->fetch_assoc();

    if ($tx) {
        if ($tx['buyer_id'] == $user_id && $tx['seller_rating'] === null) {
            // buyer rates seller
            $rated_id = (int)$tx['seller_id'];
            $up = $conn->prepare("UPDATE transactions SET seller_rating=? WHERE id=?");
            $up->bind_param('ii', $rating, $transaction_id);
            if ($up->execute()) {
                apply_user_rating($rated_id, $rating);
            }
        } elseif ($tx['seller_id'] == $user_id && $tx['buyer_rating'] === null) {
            // seller rates buyer
            $rated_id = (int)$tx['buyer_id'];
            $up = $conn->prepare("UPDATE transactions SET buyer_rating=? WHERE id=?");
            $up->bind_param('ii', $rating, $transaction_id);
            if ($up->execute()) {
                apply_user_rating($rated_id, $rating);
            }
        }
    }
    header('Location: my_history.php');
    exit;
}

// get buy and sell transactions
$sql = "SELECT t.id, t.amount, t.status, t.transaction_date, t.buyer_rating, t.seller_rating, i.title, i.image_url, buyer.id AS buyer_id, buyer.name AS buyer_name, seller.id AS seller_id, seller.name AS seller_name, CASE WHEN t.buyer_id = ? THEN 'bought' WHEN t.seller_id = ? THEN 'sold' END AS user_role
        FROM transactions t
        JOIN items i ON t.item_id = i.id
        JOIN users buyer ON t.buyer_id = buyer.id
        JOIN users seller ON t.seller_id = seller.id
        WHERE (t.buyer_id = ? OR t.seller_id = ?)
        ORDER BY t.transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

// Get lending requests
$lending_sql = "SELECT lr.id, lr.item_name, lr.description, lr.status, lr.requested_at, lr.approved_at, requester.id AS requester_id, requester.name AS requester_name, approver.id AS approver_id, approver.name AS approver_name, CASE WHEN lr.requester_id = ? THEN 'borrowed' WHEN lr.approved_by = ? THEN 'lent' END AS user_role
                FROM lending_requests lr
                JOIN users requester ON lr.requester_id = requester.id
                LEFT JOIN users approver ON lr.approved_by = approver.id
                WHERE (lr.requester_id = ? OR lr.approved_by = ?)
                ORDER BY lr.requested_at DESC";
$lending_stmt = $conn->prepare($lending_sql);
$lending_stmt->bind_param('iiii', $user_id, $user_id, $user_id, $user_id);
$lending_stmt->execute();
$lending_result = $lending_stmt->get_result();
$lending_transactions = [];
while ($row = $lending_result->fetch_assoc()) {
    $lending_transactions[] = $row;
}

// combine and sort both arrays
$all_transactions = array_merge($transactions, $lending_transactions);
usort($all_transactions, function($a, $b) {
    $date_a = isset($a['transaction_date']) ? $a['transaction_date'] : $a['approved_at'];
    $date_b = isset($b['transaction_date']) ? $b['transaction_date'] : $b['approved_at'];
    return strtotime($date_b) - strtotime($date_a);
});
?>


<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 mb-0">My Transaction History</h1>
</div>

<?php if (empty($all_transactions)): ?>
  <div class="alert alert-info">
    <i class="fa-solid fa-info-circle me-2"></i>
    You have no transaction history yet.
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($all_transactions as $transaction): ?>
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-2">
                <?php if (isset($transaction['amount'])): ?>
                  <?php if (!empty($transaction['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($transaction['image_url']); ?>"
                         class="img-fluid rounded"
                         alt="<?php echo htmlspecialchars($transaction['title']); ?>"
                         style="max-height:80px;object-fit:cover;">
                  <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:80px;">
                      <i class="fa-solid fa-image text-muted"></i>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
              <div class="col-md-4">
                <h6 class="mb-1"><?php echo htmlspecialchars($transaction['title'] ?? $transaction['item_name']); ?></h6>
                <div class="small text-muted">
                  <?php
                  $counterpart_id = null;
                  $counterpart_name = '';
                  if (isset($transaction['amount'])) {
                      if ($transaction['user_role'] === 'bought') {
                          $counterpart_id = $transaction['seller_id'];
                          $counterpart_name = $transaction['seller_name'];
                          echo 'Seller: ' . htmlspecialchars($counterpart_name);
                      } else {
                          $counterpart_id = $transaction['buyer_id'];
                          $counterpart_name = $transaction['buyer_name'];
                          echo 'Buyer: ' . htmlspecialchars($counterpart_name);
                      }
                  } else {
                      if ($transaction['user_role'] === 'borrowed') {
                          $counterpart_id = $transaction['approver_id'];
                          $counterpart_name = $transaction['approver_name'] ?? 'Pending';
                          echo 'Lender: ' . htmlspecialchars($counterpart_name);
                      } else {
                          $counterpart_id = $transaction['requester_id'];
                          $counterpart_name = $transaction['requester_name'];
                          echo 'Borrower: ' . htmlspecialchars($counterpart_name);
                      }
                  }
                  ?>
                </div>
              </div>
              <?php
              $isTx   = isset($transaction['amount']);
              $role   = $transaction['user_role'] ?? '';
              $status = $transaction['status'] ?? '';

              $roleClass = $isTx
                  ? (['bought'=>'success', 'sold'=>'primary'][$role] ?? 'secondary')
                  : (['borrowed'=>'warning', 'lent'=>'info'][$role] ?? 'secondary');

              $statusClass = [
                  'pending'=>'warning','completed'=>'success','cancelled'=>'danger',
                  'approved'=>'success','rejected'=>'danger','returned'=>'info'
              ][$status] ?? 'secondary';
              ?>
              <div class="col-md-2">
                <span class="badge bg-<?php echo $roleClass; ?>">
                  <?php echo ucfirst($role); ?>
                </span>
              </div>
              <div class="col-md-2">
                <span class="badge bg-<?php echo $statusClass; ?>">
                  <?php echo ucfirst($status); ?>
                </span>
              </div>
              <div class="col-md-2 text-end">
                <?php if (isset($transaction['amount'])): ?>
                  <div class="fw-bold">৳ <?php echo number_format((float)$transaction['amount'], 2); ?></div>
                <?php endif; ?>
                <div class="small text-muted">
                  <?php
                  $date = $transaction['transaction_date'] ?? $transaction['requested_at'];
                  echo date('M j, Y', strtotime($date));
                  ?>
                </div>
              </div>
            </div>

            <?php
            $showRatingForm = false;
            $currentGivenRating = null;
            if (isset($transaction['amount'])) {
                if ($transaction['user_role'] === 'bought') {
                    $currentGivenRating = $transaction['seller_rating'];
                    $showRatingForm = ($transaction['seller_rating'] === null);
                    $counterpart_id = $transaction['seller_id'];
                    $counterpart_name = $transaction['seller_name'];
                } else {
                    $currentGivenRating = $transaction['buyer_rating'];
                    $showRatingForm = ($transaction['buyer_rating'] === null);
                    $counterpart_id = $transaction['buyer_id'];
                    $counterpart_name = $transaction['buyer_name'];
                }
            }
            ?>
            <?php if ($showRatingForm && $counterpart_id != $user_id): ?>
              <div class="mt-2">
                <form method="post" class="d-inline-block">
                  <input type="hidden" name="transaction_id" value="<?php echo (int)$transaction['id']; ?>">
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <label class="me-2 mb-0">Rate <?php echo htmlspecialchars($counterpart_name); ?>:</label>
                    <?php for ($i=1;$i<=5;$i++): ?>
                      <input type="radio" class="btn-check" name="rating" id="rate<?php echo $transaction['id'].'_'.$i; ?>" value="<?php echo $i; ?>" required>
                      <label class="btn btn-sm btn-outline-warning" for="rate<?php echo $transaction['id'].'_'.$i; ?>">
                        <i class="fa-solid fa-star"></i> <?php echo $i; ?>
                      </label>
                    <?php endfor; ?>
                    <button type="submit" class="btn btn-sm btn-primary ms-1">Submit</button>
                  </div>
                </form>
              </div>
            <?php elseif ($currentGivenRating !== null): ?>
              <div class="mt-2 small text-muted">
                You rated <?php echo htmlspecialchars($counterpart_name); ?>: 
                <strong><?php echo (int)$currentGivenRating; ?>/5</strong>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
