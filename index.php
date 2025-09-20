
<!DOCTYPE html>
<html>
<head>
    <title>TripPlanner - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body 
        {
            font-family: 'Arial', sans-serif;
            background:url('image.png') no-repeat center center fixed;
            background-size: cover;
        }
        .card.p-5.shadow-sm.text-center{
            background: rgba(255,255,255,0.85);
            border-radius: 15px;opacity: 0.9;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
        }
        .btn.btn-primary.mb-2.w-100 {
            background:linear-gradient(90deg, #3dadc1ff, #00b4d8);color:#fff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn.btn-primary.mb-2.w-100:hover{transform:scale(1.05);box-shadow:0 4px 10px black;background:linear-gradient(90deg, #02c39a, #00a896);}
        .btn.btn-outline-primary.w-100 {
            border-color: #00b4d8;
            color: #00b4d8;
        }
        .btn.btn-outline-primary.w-100:hover { 
            background: #00b4d8;
            color: #fff;
        }
    </style>
</head>
<body class="bg-light">
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-5 shadow-sm text-center" style="max-width: 400px; width: 100%;">
        <h1 class="mb-4">Welcome to TripPlanner</h1>
        <p class="mb-4">Manage your group trip expenses easily and transparently.</p>
        <a href="login.php" class="btn btn-primary mb-2 w-100">Login</a>
        <a href="signup.php" class="btn btn-outline-primary w-100">Sign Up</a>
    </div>
</div>
</body>
</html>