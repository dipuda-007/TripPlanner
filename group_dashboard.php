<?php

session_start();
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}
require_once 'db_connect.php';

if (!isset($_GET['group_id']) || !is_numeric($_GET['group_id'])) {
    echo "Invalid group ID.";
    exit();
}

$group_id = intval($_GET['group_id']);

// Fetch group info (add more management features as needed)
$stmt = $conn->prepare('SELECT group_name, creator, country, currency, created_at FROM groups WHERE id = ?');
$stmt->bind_param('i', $group_id);
$stmt->execute();
$stmt->bind_result($group_name, $creator, $country, $currency, $created_at);
if (!$stmt->fetch()) {
    echo "Group not found.";
    exit();
}
$stmt->close();

// Fetch group members
$memStmt = $conn->prepare('SELECT user_name, email, full_name, status FROM group_members WHERE group_id = ?');
$memStmt->bind_param('i', $group_id);
$memStmt->execute();
$memStmt->bind_result($user_name, $email, $full_name, $status);

$members = [];
while ($memStmt->fetch()) {
    $members[] = [
        'user_name' => $user_name,
        'email' => $email,
        'full_name' => $full_name,
        'status' => $status
    ];
}
$memStmt->close();

if (isset($_POST['add_expense'])) {
    $paid_by = $_POST['paid_by'];
    $amount = floatval($_POST['amount']);
    $description = trim($_POST['description']);
    $split = $amount / count($members);

    // Correct parameter types: i (int), s (string), d (double), s (string), d (double)
    $expStmt = $conn->prepare('INSERT INTO expenses (group_id, paid_by, amount, description, split_amount) VALUES (?, ?, ?, ?, ?)');
    $expStmt->bind_param('isdsd', $group_id, $paid_by, $amount, $description, $split);
    $expStmt->execute();
    $expStmt->close();

    // Reload after successful insert
    header("Location: group_dashboard.php?group_id=$group_id");
    exit();
}

// Fetch all expenses for this group
$expenseStmt = $conn->prepare('SELECT paid_by, amount, description, split_amount FROM expenses WHERE group_id = ?');
$expenseStmt->bind_param('i', $group_id);
$expenseStmt->execute();
$expenseStmt->bind_result($paid_by, $amount, $description, $split_amount);

$expenses = [];
while ($expenseStmt->fetch()) {
    $expenses[] = [
        'paid_by' => $paid_by,
        'amount' => $amount,
        'description' => $description,
        'split_amount' => $split_amount
    ];
}
$expenseStmt->close();

// Calculate balances
$balances = [];
foreach ($members as $m) {
    $balances[$m['user_name']] = 0;
}
foreach ($expenses as $exp) {
    foreach ($members as $m) {
        if ($m['user_name'] !== $exp['paid_by']) {
            $balances[$m['user_name']] -= $exp['split_amount'];
            $balances[$exp['paid_by']] += $exp['split_amount'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Group Dashboard - TripPlanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Group Dashboard: <?php echo htmlspecialchars($group_name); ?></h2>
    <p><strong>Created by:</strong> <?php echo htmlspecialchars($creator); ?></p>
    <p><strong>Country:</strong> <?php echo htmlspecialchars($country); ?></p>
    <p><strong>Currency:</strong> <?php echo htmlspecialchars($currency); ?></p>
    <p><strong>Created at:</strong> <?php echo htmlspecialchars($created_at); ?></p>

    <h4>Group Members</h4>
    <?php if (count($members) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Full Name</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($m['full_name']); ?></td>
                       
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No members in this group.</p>
    <?php endif; ?>

    <!-- Add Expense Button -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#expenseModal">
    Add Expense
</button>

<!-- Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="expenseModalLabel">Add Expense</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="paid_by" class="form-label">Paid By</label>
            <select class="form-select" id="paid_by" name="paid_by" required>
              <?php foreach ($members as $m): ?>
                <option value="<?php echo htmlspecialchars($m['user_name']); ?>">
                  <?php echo htmlspecialchars($m['full_name']); ?> (<?php echo htmlspecialchars($m['user_name']); ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="amount" class="form-label">Amount (<?php echo htmlspecialchars($currency); ?>)</label>
            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_expense" class="btn btn-primary">Add Expense</button>
        </div>
      </div>
    </form>
  </div>
</div>

    <h4>Expense Balances</h4>
    <?php foreach ($members as $m): ?>
        <?php if ($m['user_name'] !== $_SESSION['user_name']): ?>
            <?php if ($balances[$_SESSION['user_name']] < 0): ?>
                <div class="alert alert-warning">
                    You owe <?php echo htmlspecialchars($m['full_name']); ?> (<?php echo htmlspecialchars($m['user_name']); ?>)
                    <?php echo htmlspecialchars(abs($balances[$_SESSION['user_name']])); ?> <?php echo htmlspecialchars($currency); ?>
                </div>
            <?php elseif ($balances[$_SESSION['user_name']] > 0): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($m['full_name']); ?> (<?php echo htmlspecialchars($m['user_name']); ?>)
                    owes you <?php echo htmlspecialchars($balances[$_SESSION['user_name']]); ?> <?php echo htmlspecialchars($currency); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <a href="group.php" class="btn btn-outline-secondary mt-3">Back to My Groups</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>