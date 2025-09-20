<?php

session_start();
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}
require_once 'db_connect.php';

$user_name = $_SESSION['user_name'];

// Accept invitation
if (isset($_GET['accept']) && is_numeric($_GET['accept'])) {
    $group_id = intval($_GET['accept']);
    $stmt = $conn->prepare('UPDATE group_members SET status = "accepted" WHERE group_id = ? AND user_name = ?');
    $stmt->bind_param('is', $group_id, $user_name);
    $stmt->execute();
}

// Fetch invitations
$invStmt = $conn->prepare('SELECT gm.group_id, g.group_name, g.creator FROM group_members gm JOIN groups g ON gm.group_id = g.id WHERE gm.user_name = ? AND gm.status = "invited"');
$invStmt->bind_param('s', $user_name);
$invStmt->execute();
$invStmt->bind_result($group_id, $group_name, $creator);

$invitations = [];
while ($invStmt->fetch()) {
    $invitations[] = ['group_id' => $group_id, 'group_name' => $group_name, 'creator' => $creator];
}
$invStmt->close();

// Fetch accepted groups
$grpStmt = $conn->prepare('SELECT g.id, g.group_name, g.creator FROM group_members gm JOIN groups g ON gm.group_id = g.id WHERE gm.user_name = ? AND gm.status = "accepted"');
$grpStmt->bind_param('s', $user_name);
$grpStmt->execute();
$grpStmt->bind_result($group_id, $group_name, $creator);

$groups = [];
while ($grpStmt->fetch()) {
    $groups[] = ['group_id' => $group_id, 'group_name' => $group_name, 'creator' => $creator];
}
$grpStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Groups - TripPlanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?></h2>
    <h4>Your Groups</h4>
    <?php if (count($groups) > 0): ?>
        <ul class="list-group mb-4">
            <?php foreach ($groups as $group): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($group['group_name']); ?></strong>
                        <span class="text-muted"> (Created by <?php echo htmlspecialchars($group['creator']); ?>)</span>
                    </div>
                    <div>
                        <a href="group_dashboard.php?group_id=<?php echo $group['group_id']; ?>" class="btn btn-sm btn-success">Open</a>
                        <a href="group_info.php?group_id=<?php echo $group['group_id']; ?>" class="btn btn-sm btn-info">View</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have not joined any groups yet.</p>
    <?php endif; ?>

    <h4>Invitations</h4>
    <?php if (count($invitations) > 0): ?>
        <ul class="list-group">
            <?php foreach ($invitations as $invite): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($invite['group_name']); ?></strong>
                    <span class="text-muted"> (Invited by <?php echo htmlspecialchars($invite['creator']); ?>)</span>
                    <a href="group.php?accept=<?php echo $invite['group_id']; ?>" class="btn btn-success btn-sm float-end">Accept</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No invitations at the moment.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-outline-secondary mt-4">Back to Dashboard</a>
</div>
</body>
</html>