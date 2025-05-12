<?php
// ekhane database er shathe connect korse
include 'db_connect.php';

// success ar error er hishab
$error = "";
$success = "";

// form submitted hoise naki check
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // dui ta password mathcing korano 
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // username jodi already ei nam e thake ta check kora 
        $sql_check = "SELECT * FROM users WHERE user_name = '$username'";
        $result_check = $conn->query($sql_check);

        if ($result_check && $result_check->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            // password hash kkore neya use er age, eita chara bohut kichu kaj kore na 
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // database e notun user insert korano 
            $sql_insert = "INSERT INTO users (user_name, password) VALUES ('$username', '$hashed_password')";
            if ($conn->query($sql_insert) === TRUE) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Research Hive</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #28a745, #007bff);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .register-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
        }

        .register-container h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .register-container form {
            display: flex;
            flex-direction: column;
        }

        .register-container input {
            margin-bottom: 15px;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        .register-container input:focus {
            border-color: #28a745;
        }

        .register-container button {
            padding: 12px;
            font-size: 16px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .register-container button:hover {
            background-color: #1e7e34;
        }

        .register-container .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .register-container .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .register-container .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .register-container .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .register-container .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Create an Account</h1>
        <p>Join Research Hive today</p>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        <div class="footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>