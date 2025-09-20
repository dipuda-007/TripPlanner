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
        $userStmt = $conn->prepare('SELECT user_name, email, full_name FROM users WHERE user_name = ?');
        $userStmt->bind_param('s', $creator);
        $userStmt->execute();
        $userStmt->bind_result($uname, $email, $full_name);
        if ($userStmt->fetch()) {
            $userStmt->close();
            $stmt2 = $conn->prepare('INSERT INTO group_members (group_id, user_name, email, full_name) VALUES (?, ?, ?, ?)');
            $stmt2->bind_param('isss', $group_id, $uname, $email, $full_name);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $userStmt->close();
        }

        // Add other members
        foreach ($members as $member) {
            if ($member !== $creator && $member !== '') {
                $checkStmt = $conn->prepare('SELECT user_name, email, full_name FROM users WHERE user_name = ?');
                $checkStmt->bind_param('s', $member);
                $checkStmt->execute();
                $checkStmt->bind_result($uname, $email, $full_name);
                if ($checkStmt->fetch()) {
                    $checkStmt->close();
                    $stmt2 = $conn->prepare('INSERT INTO group_members (group_id, user_name, email, full_name) VALUES (?, ?, ?, ?)');
                    $stmt2->bind_param('isss', $group_id, $uname, $email, $full_name);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    $checkStmt->close();
                }
            }
        }
        $message = 'Group created successfully!';
        header('Location: groups.php');

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        .member-list { margin-top: 10px; }
        .member-item { display: inline-block; background: #e3f2fd; padding: 5px 10px; border-radius: 12px; margin: 2px; }
        .remove-member { color: #d32f2f; cursor: pointer; margin-left: 8px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</div>
        <?php if ($message): ?>
            <div class="alert alert-info"> <?php echo $message; ?> </div>
        <?php endif; ?>
        <h4>Create a Group</h4>
        <form method="POST" class="mb-4" id="groupForm">
            <div class="mb-3">
                <label for="group_name" class="form-label">Group Name</label>
                <input type="text" class="form-control" id="group_name" name="group_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Add Members</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search_user" placeholder="Enter user name">
                    <button type="button" class="btn btn-outline-primary" id="add_member_btn">Add</button>
                </div>
                <div id="search_result" class="mt-2"></div>
                <div class="member-list" id="member_list"></div>
            </div>
            <input type="hidden" name="members" id="members_hidden">
            <button type="submit" class="btn btn-primary">Create Group</button>
        </form>
        <a href="group.php" class="btn btn-info mt-2">View My Groups & Invitations</a>
        <a href="logout.php" class="btn btn-outline-secondary mt-2">Logout</a>
    </div>
    <script>
        let members = [];
        $('#add_member_btn').click(function() {
            let user = $('#search_user').val().trim();
            if (!user || members.includes(user)) return;
            $.get('search_user.php', {user_name: user}, function(data) {
                if (data === 'found') {
                    members.push(user);
                    updateMemberList();
                    $('#search_result').html('<span class="text-success">User added!</span>');
                } else {
                    $('#search_result').html('<span class="text-danger">User not found!</span>');
                }
            });
        });
        function updateMemberList() {
            $('#member_list').html('');
            members.forEach(function(user, idx) {
                $('#member_list').append(
                    `<span class="member-item">${user} <span class="remove-member" onclick="removeMember(${idx})">&times;</span></span>`
                );
            });
            $('#members_hidden').val(members.join(','));
        }
        window.removeMember = function(idx) {
            members.splice(idx, 1);
            updateMemberList();
        }
    </script>
</body>
</html>