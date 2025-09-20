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

// Fetch group info
$stmt = $conn->prepare('SELECT group_name, creator, created_at FROM groups WHERE id = ?');
$stmt->bind_param('i', $group_id);
$stmt->execute();
$stmt->bind_result($group_name, $creator, $created_at);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Group Info - TripPlanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Group: <?php echo htmlspecialchars($group_name); ?></h2>
    <p><strong>Created by:</strong> <?php echo htmlspecialchars($creator); ?></p>
    <p><strong>Created at:</strong> <?php echo htmlspecialchars($created_at); ?></p>
    <h4>Members</h4>
    <?php if (count($members) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $m): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($m['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($m['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($m['email']); ?></td>
                        <td><?php echo htmlspecialchars($m['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No members in this group.</p>
    <?php endif; ?>
    <a href="group.php" class="btn btn-outline-secondary mt-3">Back to My Groups</a>
    <a href="group_info.php?group_id=<?php echo $group['group_id']; ?>" class="btn btn-sm btn-info">View Details</a>
</div>
</body>
</html>