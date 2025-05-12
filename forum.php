<?php
// ekhane database er shathe connect korse
include 'db_connect.php';

// Start a session to manage user login
session_start();

// bhai ki log in korse
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$error = "";
$success = "";

// Fetch the user_id of the logged-in user
$username = $_SESSION['username'];
$sql_user = "SELECT user_id FROM users WHERE user_name = '$username'";
$result_user = $conn->query($sql_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $user_id = $user['user_id'];

    // post banano handle
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_post'])) {
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);

        $sql = "INSERT INTO forum_posts (user_id, type, title, content) VALUES 
                ($user_id, '$type', '$title', '$content')";
        if ($conn->query($sql) === TRUE) {
            $success = "Post created successfully!";
        } else {
            $error = "Error creating post: " . $conn->error;
        }
    }

    // edike upvote er hishab nikash
    if (isset($_GET['upvote_id'])) {
        $post_id = intval($_GET['upvote_id']);
        $sql_upvote = "UPDATE forum_posts SET upvotes = upvotes + 1 WHERE id = $post_id";
        if ($conn->query($sql_upvote) === TRUE) {
            $success = "Post upvoted successfully!";
        } else {
            $error = "Error upvoting post: " . $conn->error;
        }
    }

    // oi j commenting
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_comment'])) {
        $post_id = intval($_POST['post_id']);
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);

        $sql_comment = "INSERT INTO forum_comments (post_id, user_id, comment) VALUES 
                        ($post_id, $user_id, '$comment')";
        if ($conn->query($sql_comment) === TRUE) {
            $success = "Comment added successfully!";
        } else {
            $error = "Error adding comment: " . $conn->error;
        }
    }

    // delete kore dilam kiintu
    if (isset($_GET['delete_post_id'])) {
        $post_id = intval($_GET['delete_post_id']);

        // associated comment delete
        $sql_delete_comments = "DELETE FROM forum_comments WHERE post_id = $post_id";
        $conn->query($sql_delete_comments);

        // Delete the post
        $sql_delete_post = "DELETE FROM forum_posts WHERE id = $post_id AND user_id = $user_id";
        if ($conn->query($sql_delete_post) === TRUE) {
            $success = "Post deleted successfully!";
        } else {
            $error = "Error deleting post: " . $conn->error;
        }
    }

    //  comment deletion handle kora
    if (isset($_GET['delete_comment_id'])) {
        $comment_id = intval($_GET['delete_comment_id']);
        $sql_delete_comment = "DELETE FROM forum_comments WHERE id = $comment_id AND user_id = $user_id";
        if ($conn->query($sql_delete_comment) === TRUE) {
            $success = "Comment deleted successfully!";
        } else {
            $error = "Error deleting comment: " . $conn->error;
        }
    }
} else {
    $error = "User not found.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Research Hive</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            position: relative;
        }

        header h1 {
            margin: 0;
            text-align: center;
        }

        .dashboard-button {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .dashboard-button:hover {
            background-color: #1e7e34;
        }

        .search-box {
            margin: 20px auto;
            max-width: 800px;
            text-align: center;
        }

        .search-box input[type="text"] {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .search-box button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-box button:hover {
            background-color: #0056b3;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 30px;
        }

        form input, form textarea, form select, form button {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .post {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .post h2 {
            margin: 0 0 10px;
        }

        .post .meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .post .actions a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }

        .post .actions a:hover {
            text-decoration: underline;
        }

        .comments {
            margin-top: 20px;
        }

        .comments h3 {
            margin-bottom: 10px;
        }

        .comments .comment {
            border-top: 1px solid #ddd;
            padding: 10px 0;
        }

        .comments .comment:first-child {
            border-top: none;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Forum</h1>
        <a href="dashboard.php" class="dashboard-button">Dashboard</a>
    </header>

    <!-- Search Box -->
    <div class="search-box">
        <form method="GET" action="forum.php">
            <input type="text" name="search" placeholder="Search posts..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="container">
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <select name="type" required>
                <option value="">Select Type</option>
                <option value="Ideas">Ideas</option>
                <option value="Issues">Issues</option>
            </select>
            <input type="text" name="title" placeholder="Post Title" required>
            <textarea name="content" placeholder="Write your post here..." rows="5" required></textarea>
            <button type="submit" name="create_post">Create Post</button>
        </form>
        <div class="posts">
            <?php
            // Handle search query
            $search_query = "";
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = mysqli_real_escape_string($conn, $_GET['search']);
                $search_query = "WHERE forum_posts.title LIKE '%$search%' OR forum_posts.content LIKE '%$search%'";
            }

            // Fetch posts based on search query
            $sql_posts = "SELECT forum_posts.*, users.user_name FROM forum_posts 
                          JOIN users ON forum_posts.user_id = users.user_id 
                          $search_query
                          ORDER BY forum_posts.created_at DESC";
            $result_posts = $conn->query($sql_posts);

            if ($result_posts && $result_posts->num_rows > 0): ?>
                <?php while ($post = $result_posts->fetch_assoc()): ?>
                    <div class="post">
                        <h2><?php echo $post['title']; ?></h2>
                        <div class="meta">
                            Posted by <?php echo $post['user_name']; ?> on <?php echo $post['created_at']; ?> | Type: <?php echo $post['type']; ?>
                        </div>
                        <p><?php echo $post['content']; ?></p>
                        <div class="actions">
                            <a href="?upvote_id=<?php echo $post['id']; ?>">Upvote (<?php echo $post['upvotes']; ?>)</a>
                            <?php if ($post['user_id'] == $user_id): ?>
                                | <a href="?delete_post_id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                            <?php endif; ?>
                        </div>
                        <div class="comments">
                            <h3>Comments</h3>
                            <?php
                            $post_id = $post['id'];
                            $sql_comments = "SELECT forum_comments.*, users.user_name FROM forum_comments 
                                             JOIN users ON forum_comments.user_id = users.user_id 
                                             WHERE forum_comments.post_id = $post_id 
                                             ORDER BY forum_comments.created_at ASC";
                            $result_comments = $conn->query($sql_comments);
                            ?>
                            <?php if ($result_comments && $result_comments->num_rows > 0): ?>
                                <?php while ($comment = $result_comments->fetch_assoc()): ?>
                                    <div class="comment">
                                        <strong><?php echo $comment['user_name']; ?>:</strong>
                                        <p><?php echo $comment['comment']; ?></p>
                                        <?php if ($comment['user_id'] == $user_id): ?>
                                            <a href="?delete_comment_id=<?php echo $comment['id']; ?>" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>No comments yet.</p>
                            <?php endif; ?>
                            <form method="POST">
                                <textarea name="comment" placeholder="Write a comment..." rows="2" required></textarea>
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="add_comment">Add Comment</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>