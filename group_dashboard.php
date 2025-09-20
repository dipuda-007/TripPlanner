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

// Initialize balances between each pair
$balances = [];
foreach ($members as $payer) {
    foreach ($members as $receiver) {
        if ($payer['user_name'] !== $receiver['user_name']) {
            $balances[$payer['user_name']][$receiver['user_name']] = 0;
        }
    }
}

// For each expense, update balances
foreach ($expenses as $exp) {
    foreach ($members as $m) {
        if ($m['user_name'] !== $exp['paid_by']) {
            // Each member owes split_amount to the payer
            $balances[$m['user_name']][$exp['paid_by']] += $exp['split_amount'];
        }
    }
}

// Net out balances between each pair
$net_balances = [];
foreach ($members as $m1) {
    foreach ($members as $m2) {
        if ($m1['user_name'] !== $m2['user_name']) {
            $owed = $balances[$m1['user_name']][$m2['user_name']] - $balances[$m2['user_name']][$m1['user_name']];
            if ($owed > 0) {
                $net_balances[$m1['user_name']][$m2['user_name']] = $owed;
            }
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
<nav class="navbar navbar-light bg-light justify-content-end">
    <span class="navbar-text me-3">
        Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
    </span>
    <a href="logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
</nav>
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
        <?php
        if ($m['user_name'] !== $_SESSION['user_name']) {
            $balance = $balances[$_SESSION['user_name']][$m['user_name']];
            if ($balance < 0) {
                ?>
                <div class="alert alert-warning">
                    You owe <?php echo htmlspecialchars($m['full_name']); ?> (<?php echo htmlspecialchars($m['user_name']); ?>)
                    <?php echo htmlspecialchars(abs($balance)); ?> <?php echo htmlspecialchars($currency); ?>
                </div>
                <?php
            } elseif ($balance > 0) {
                ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($m['full_name']); ?> (<?php echo htmlspecialchars($m['user_name']); ?>)
                    owes you <?php echo htmlspecialchars($balance); ?> <?php echo htmlspecialchars($currency); ?>
                </div>
                <?php
            }
        }
        ?>
    <?php endforeach; ?>

    <h4>Who Owes Whom</h4>
    <?php
    foreach ($members as $m1) {
        foreach ($members as $m2) {
            if (
                $m1['user_name'] !== $m2['user_name'] &&
                isset($net_balances[$m1['user_name']][$m2['user_name']]) &&
                $net_balances[$m1['user_name']][$m2['user_name']] > 0
            ) {
                echo '<div class="alert alert-warning">';
                echo htmlspecialchars($m1['full_name']) . ' (' . htmlspecialchars($m1['user_name']) . ') owes ';
                echo htmlspecialchars($m2['full_name']) . ' (' . htmlspecialchars($m2['user_name']) . ') ';
                echo htmlspecialchars(number_format($net_balances[$m1['user_name']][$m2['user_name']], 2)) . ' ' . htmlspecialchars($currency);
                echo '</div>';
            }
        }
    }
    ?>

    <h4>Group Expense Summary</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Member</th>
            <th>Total Paid (<?php echo htmlspecialchars($currency); ?>)</th>
            <th>Share of Expenses (<?php echo htmlspecialchars($currency); ?>)</th>
            <th>Balance (<?php echo htmlspecialchars($currency); ?>)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Calculate total paid and share for each member
        $total_paid = [];
        $share = [];
        $balance = [];
        $member_count = count($members);

        foreach ($members as $m) {
            $total_paid[$m['user_name']] = 0;
            $share[$m['user_name']] = 0;
        }

        foreach ($expenses as $exp) {
            $total_paid[$exp['paid_by']] += $exp['amount'];
            foreach ($members as $m) {
                $share[$m['user_name']] += $exp['split_amount'];
            }
        }

        foreach ($members as $m) {
            $balance[$m['user_name']] = $total_paid[$m['user_name']] - $share[$m['user_name']];
            echo '<tr>';
            echo '<td>' . htmlspecialchars($m['full_name']) . ' (' . htmlspecialchars($m['user_name']) . ')</td>';
            echo '<td>' . htmlspecialchars(number_format($total_paid[$m['user_name']], 2)) . '</td>';
            echo '<td>' . htmlspecialchars(number_format($share[$m['user_name']], 2)) . '</td>';
            $bal = $balance[$m['user_name']];
            $bal_str = ($bal >= 0 ? '+' : '') . number_format($bal, 2);
            echo '<td>' . $bal_str . ($bal >= 0 ? ' (is owed)' : ' (owes)') . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

    <a href="group.php" class="btn btn-outline-secondary mt-3">Back to My Groups</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Example conversion rates (1 USD to X)
$conversion_rates = [
    'USD' => 1,
    'INR' => 83,      // 1 USD = 83 INR
    'JPY' => 157,     // 1 USD = 157 JPY
    'EUR' => 0.93,    // 1 USD = 0.93 EUR
    // Add more as needed
];
?>