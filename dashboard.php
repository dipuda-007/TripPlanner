<?php

session_start();
if (!isset($_SESSION['user_name'])) {
    header('Location: login.php');
    exit();
}
require_once 'db_connect.php';

// Handle group creation
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'], $_POST['members'])) {
    $group_name = trim($_POST['group_name']);
    $members = array_map('trim', explode(',', $_POST['members']));
    $creator = $_SESSION['user_name'];

    // Insert group
    $stmt = $conn->prepare('INSERT INTO groups (group_name, creator) VALUES (?, ?)');
    $stmt->bind_param('ss', $group_name, $creator);
    if ($stmt->execute()) {
        $group_id = $stmt->insert_id;
        // Add creator to group_members
        $stmt2 = $conn->prepare('INSERT INTO group_members (group_id, user_name) VALUES (?, ?)');
        $stmt2->bind_param('is', $group_id, $creator);
        $stmt2->execute();
        // Add other members
         foreach ($members as $member) {
            if ($member !== $creator && $member !== '') {
                // Check if user exists
                $checkStmt = $conn->prepare('SELECT id FROM users WHERE user_name = ?');
                $checkStmt->bind_param('s', $member);
                $checkStmt->execute();
                $checkStmt->store_result();
                if ($checkStmt->num_rows > 0) {
                    $stmt2->bind_param('is', $group_id, $member);
                    $stmt2->execute();
                }
                $checkStmt->close();
            }
        }
        $message = 'Group created successfully!';
    } else {
        $message = 'Error creating group.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TripPlanner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            min-height: 100vh;
        }
        .dashboard-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            padding: 32px;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            border: none;
        }
        .welcome {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 24px;
            color: #185a9d;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</div>
        <?php if ($message): ?>
            <div class="alert alert-info"> <?php echo $message; ?> </div>
        <?php endif; ?>
        <h4>Create a Group</h4>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="group_name" class="form-label">Group Name</label>
                <input type="text" class="form-control" id="group_name" name="group_name" required>
            </div>
            <div class="mb-3">
                <label for="members" class="form-label">Add Members (comma separated user names)</label>
                <input type="text" class="form-control" id="members" name="members" placeholder="e.g. alice, bob, charlie">
            </div>
            <button type="submit" class="btn btn-primary">Create Group</button>
        </form>
        <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
    </div>
</body>
</html>