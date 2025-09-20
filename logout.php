<?php
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>
<a href="logout.php" class="btn btn-outline-secondary mt-2">Logout</a>