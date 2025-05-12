<?php
session_start();

// login na hole redirect korbe
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// file download stuff
if (isset($_GET['download']) && !empty($_GET['file'])) {
    $file_path = $_GET['file'];

    // file path correctvhabe handle kora jate dictionary ralated problem na hoy 
    $file_path = realpath($file_path);
    if ($file_path && file_exists($file_path)) {
        // Force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit();
    } else {
        echo "Invalid file path.";
        exit();
    }
}

include 'db_connect.php'; // Ensure this file connects to your database

// logged in user er data input
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session
$stmt = $conn->prepare("SELECT user_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    $username = htmlspecialchars($user['user_name']);
} else {
    $username = "Unknown User"; // Fallback if the user is not found
}

// connected user er data add kora dashboard e like commeent file and stuff from repo
$stmt = $conn->prepare("
    SELECT r.title, r.description, r.file_path, r.tags, p.researcher_name 
    FROM repository r
    JOIN connections c ON (c.user_id = ? AND c.connected_user_id = r.user_id)
    JOIN profile p ON p.user_id = r.user_id
    ORDER BY r.id DESC
    LIMIT 4
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$repo_result = $stmt->get_result();
$latest_repositories = $repo_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Research Hive</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, rgb(0, 255, 242), rgb(167, 40, 89));
            margin: 0;
            height: 170vh;
        }

        header {
            background: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .container {
            max-width: 960px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        .dashboard-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .dashboard-links a {
            background: #007bff;
            color: white;
            padding: 15px 25px;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .dashboard-links a:hover {
            background: #0056b3;
        }

        .search-box {
            text-align: center;
            margin-top: 40px;
        }

        .search-box input {
            padding: 10px;
            width: 60%;
            max-width: 400px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-box button {
            padding: 10px 20px;
            margin-left: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #218838;
        }

        .latest-repositories {
            margin-top: 40px;
        }

        .latest-repositories h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .latest-repositories ul {
            list-style: none;
            padding: 0;
        }

        .latest-repositories li {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .latest-repositories h3 {
            margin: 0 0 10px;
            color: #007bff;
        }

        .latest-repositories p {
            margin: 5px 0;
            color: #555;
        }

        .latest-repositories .tags {
            font-style: italic;
            color: #666;
        }

        .latest-repositories .file-link {
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

        .latest-repositories .file-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Research Hive</div>
        <nav class="nav-links">
            <a href="#">Welcome, <?php echo $username; ?></a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <h1>Your Dashboard</h1>
        <p style="text-align:center;">Reseach Hive er Dashboard e Apnake Shagotom.</p>

        <div class="dashboard-links">
            <a href="profile.php">Profile</a>
            <a href="repository.php">Repository</a>
            <a href="forum.php">Forum</a>
            <a href="connections.php">Connections</a>
        </div>

        <div class="search-box">
            <form method="GET" action="search.php">
                <input type="text" name="query" placeholder="Search users.." required>
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="latest-repositories">
            <h2>Latest Repositories from Your Connections</h2>
            <?php if (!empty($latest_repositories)): ?>
                <ul>
                    <?php foreach ($latest_repositories as $repo): ?>
                        <li>
                            <h3><?= htmlspecialchars($repo['title']) ?> by <?= htmlspecialchars($repo['researcher_name']) ?></h3>
                            <p><?= htmlspecialchars($repo['description']) ?></p>
                            <?php if (!empty($repo['tags'])): ?>
                                <p class="tags"><strong>Tags:</strong> <?= htmlspecialchars($repo['tags']) ?></p>
                            <?php endif; ?>
                            <a class="file-link" href="?download=1&file=<?= urlencode($repo['file_path']) ?>">View File</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No recent repositories from your connections.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
