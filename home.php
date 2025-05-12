<?php
// ekhane database er shathe connect korse
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Hive</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            
            background-color: #f9f9f9;
            color: #333;

                      
        }

        header {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff6600;
            display: inline-block;
        }

        .nav-links {
            list-style: none;
            margin: 0;
            padding: 0;
            float: right;
        }

        .nav-links li {
            display: inline;
            margin: 0 10px;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .welcome-section {
            text-align: center;
            padding: 50px 20px;
        }

        .welcome-section h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .welcome-section p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .buttons {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 10px;
        }

        .btn-login {
            background-color: #007bff;
            color: #fff;
        }

        .btn-register {
            background-color: #28a745;
            color: #fff;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Research Hive</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="welcome-section">
            <h1>Welcome to Research Hive</h1>
            <p>Research Hive is an online platform for researchers. Connect with Researchers, Sharing Research files.</p>
            <h2>LessGoooo</h2>
            <p>Login or register to access the platform.</p>
            <div class="buttons">
                <a href="login.php" class="btn btn-login">Login</a>
                <a href="register.php" class="btn btn-register">Register</a>
            </div>
        </section>
    </main>
</body>
</html>