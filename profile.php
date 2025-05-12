<?php
// ekhane database er shathe connect korse
include 'db_connect.php';

// user login manage korar jonno notun session shuru
session_start();

// log in kora ase  naki check kora 
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$error = "";
$success = "";

// user table theke user data fetch
$username = $_SESSION['username'];
$sql_user = "SELECT user_id FROM users WHERE user_name = '$username'";
$result_user = $conn->query($sql_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $user_id = $user['user_id'];

    // profile table theke profile data fetch
    $sql_profile = "SELECT * FROM profile WHERE user_id = $user_id";
    $result_profile = $conn->query($sql_profile);
    $profile = $result_profile->fetch_assoc();
} else {
    $error = "User not found.";
}

// profile update er hishab nikash
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $researcher_name = mysqli_real_escape_string($conn, $_POST['researcher_name']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $institution = mysqli_real_escape_string($conn, $_POST['institution']);
    $research_interest = mysqli_real_escape_string($conn, $_POST['research_interest']);

    // profile ki already exist kore naki ta check kora
    if ($result_profile && $result_profile->num_rows > 0) {
        // Update the existing profile
        $sql_update = "UPDATE profile SET 
            researcher_name = '$researcher_name', 
            bio = '$bio', 
            institution = '$institution', 
            research_interest = '$research_interest' 
            WHERE user_id = $user_id";
    } else {
        // notun profile insert kora
        $sql_update = "INSERT INTO profile (user_id, researcher_name, bio, institution, research_interest) VALUES 
            ($user_id, '$researcher_name', '$bio', '$institution', '$research_interest')";
    }

    if ($conn->query($sql_update) === TRUE) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Research Hive</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #007bff, #28a745);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .profile-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 500px;
            text-align: center;
        }

        .profile-container h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .profile-container form {
            display: flex;
            flex-direction: column;
        }

        .profile-container input, .profile-container textarea {
            margin-bottom: 15px;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        .profile-container input:focus, .profile-container textarea:focus {
            border-color: #007bff;
        }

        .profile-container button {
            padding: 12px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .profile-container button:hover {
            background-color: #0056b3;
        }

        .profile-container .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .profile-container .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .profile-container .home-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .profile-container .home-button:hover {
            background-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Your Profile</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="researcher_name" placeholder="Researcher Name" value="<?php echo $profile['researcher_name'] ?? ''; ?>" required>
            <textarea name="bio" placeholder="Bio" rows="4"><?php echo $profile['bio'] ?? ''; ?></textarea>
            <input type="text" name="institution" placeholder="Institution" value="<?php echo $profile['institution'] ?? ''; ?>" required>
            <textarea name="research_interest" placeholder="Research Interests" rows="4"><?php echo $profile['research_interest'] ?? ''; ?></textarea>
            <button type="submit">Update Profile</button>
        </form>
        <a href="dashboard.php" class="home-button">Dashboard</a>
    </div>
</body>
</html>