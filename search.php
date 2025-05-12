<?php
session_start();
include 'db_connect.php';  

 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// search query
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';

$results = [];
if (!empty($search_query)) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, user_name FROM users WHERE user_name LIKE ?");
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results | Research Hive</title>
    <style>
        body {


        	font-family: Arial, sans-serif;
        	background: linear-gradient(135deg,rgb(204, 0, 255),rgb(40, 159, 167));
        	margin: 0;

            height: 100vh;

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
            box-shadow: 0 4px 10px rgb(0, 0, 0);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .search-results {
            margin-top: 20px;
        }

        .search-results ul {
            list-style: none;
            padding: 0;
        }

        .search-results li {
            margin-bottom: 20px;
            padding: 15px;
            background-color:rgba(249, 249, 249, 0);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .search-results a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            font-size: 16px;
            transition: color 0.3s;
        }

        .search-results a:hover {
            color: #0056b3;
        }

        .no-results {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 20px;
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
        <h1>Search Results</h1>
        <p>Results for: <strong><?= htmlspecialchars($search_query) ?></strong></p>

        <div class="search-results">
            <?php if (!empty($results)): ?>
                <ul>
                    <?php foreach ($results as $user): ?>
                        <li>
                            <a href="view_profile.php?user_id=<?= $user['user_id'] ?>">
                                <?= htmlspecialchars($user['user_name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-results">No users found matching your search.</p>
            <?php endif; ?>
        </div>

        <div class="dashboard-button">
            <a href="Dashboard.php">Go to Dashboard</a>
        </div>
    </div>
</body>
</html>