<?php
session_start();
include 'db_connect.php';  

// log in naki check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// query string theke user id 
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    echo "Invalid user ID.";
    exit();
}

// Fetch user profile details
$stmt = $conn->prepare("SELECT researcher_name, bio, institution, research_interest FROM profile WHERE user_id = ?");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile_result = $stmt->get_result();
$profile = $profile_result->fetch_assoc();
$stmt->close();

if (!$profile) {
    echo "Profile not found.";
    exit();
}

// Fetch user's repository
$stmt = $conn->prepare("SELECT title, description, tags, file_path FROM repository WHERE user_id = ?");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$repo_result = $stmt->get_result();
$repositories = $repo_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | <?= htmlspecialchars($profile['researcher_name']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #007bff, #28a745);
            color: #333;
        }

        header {
            background-color: #fff;
            color: #333;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .container {
            max-width: 960px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .profile-details {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .profile-details p {
            margin: 10px 0;
            font-size: 16px;
            color: #555;
        }

        .repositories {
            margin-top: 30px;
        }

        .repositories h2 {
            color: #007bff;
            margin-bottom: 20px;
        }

        .repositories ul {
            list-style: none;
            padding: 0;
        }

        .repositories li {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .repositories h3 {
            margin: 0 0 10px;
            color: #007bff;
        }

        .repositories p {
            margin: 5px 0;
            color: #555;
        }

        .repositories .tags {
            font-style: italic;
            color: #666;
        }

        .repositories .file-link {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .repositories .file-link:hover {
            background-color: #0056b3;
        }

        .dashboard-button {
            text-align: center;
            margin-top: 30px;
        }

        .dashboard-button a {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
            transition: background 0.3s;
        }

        .dashboard-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Research Hive</div>
    </header>

    <div class="container">
        <h1>Profile: <?= htmlspecialchars($profile['researcher_name']) ?></h1>

        <div class="profile-details">
            <p><strong>Bio:</strong> <?= htmlspecialchars($profile['bio']) ?></p>
            <p><strong>Institution:</strong> <?= htmlspecialchars($profile['institution']) ?></p>
            <p><strong>Research Interests:</strong> <?= htmlspecialchars($profile['research_interest']) ?></p>
        </div>

        <div class="repositories">
            <h2>Repositories</h2>
            <?php if (!empty($repositories)): ?>
                <ul>
                    <?php foreach ($repositories as $repo): ?>
                        <li>
                            <h3><?= htmlspecialchars($repo['title']) ?></h3>
                            <p><?= htmlspecialchars($repo['description']) ?></p>
                            <?php if (!empty($repo['tags'])): ?>
                                <p class="tags"><strong>Tags:</strong> <?= htmlspecialchars($repo['tags']) ?></p>
                            <?php endif; ?>
                            <a class="file-link" href="<?= htmlspecialchars($repo['file_path']) ?>" target="_blank">View File</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No repositories found for this user.</p>
            <?php endif; ?>
        </div>

        <div class="dashboard-button">
            <a href="Dashboard.php">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>