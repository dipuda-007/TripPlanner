<?php

require_once 'db_connect.php';
if (isset($_GET['user_name'])) {
    $user_name = trim($_GET['user_name']);
    $stmt = $conn->prepare('SELECT id FROM users WHERE user_name = ?');
    $stmt->bind_param('s', $user_name);
    $stmt->execute();
    $stmt->store_result();
    echo $stmt->num_rows > 0 ? 'found' : 'notfound';
    $stmt->close();
}
?>