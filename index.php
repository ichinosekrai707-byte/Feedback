<?php
// ====================================================================
// FILE: index.php
// ====================================================================
session_start();
include "db.php";

// Fetch all saved feedback records from the database
$query = "SELECT f.*, u.username FROM feedback f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Feedback System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Mini Feedback System</h1>
            <div class="auth-status">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! | <a href="logout.php" class="logout-link">Logout</a></p>
                <?php else: ?>
                    <p><a href="login.php" class="login-btn">Admin Login (Optional)</a></p>
                <?php endif; ?>
            </div>
        </header>

        <!-- PHP SESSION STATUS ALERTS (SUCCESS/WARNING/ERROR) -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert <?php echo $_SESSION['msg_type']; ?>">
                <?php 
                    echo $_SESSION['msg']; 
                    unset($_SESSION['msg']);
                    unset($_SESSION['msg_type']);
                ?>
            </div>
        <?php endif; ?>

        <!-- FEEDBACK SUBMISSION CARD FORM -->
        <div class="card">
            <h2>Share Your Thoughts</h2>
            <form action="submit_feedback.php" method="POST">
                <div class="form-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" 
                           placeholder="Enter your name or Anonymous" 
                           <?php echo isset($_SESSION['username']) ? 'readonly' : ''; ?> required>
                </div>
                <div class="form-group">
                    <label for="message">Feedback Message:</label>
                    <textarea id="message" name="message" rows="5" placeholder="Write your message here... (Minimum 10 characters)" required></textarea>
                </div>
                <button type="submit" class="btn submit-btn">Submit Feedback</button>
            </form>
        </div>

        <!-- PUBLIC FEEDBACK WALL VIEW STREAM -->
        <div class="feedback-wall">
            <h2>Recent Submissions</h2>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="feedback-card <?php echo $row['user_id'] ? 'verified-user' : ''; ?>">
                        <div class="feedback-header">
                            <span class="user-badge">
                                👤 <?php echo htmlspecialchars($row['name']); ?>
                                <?php if ($row['user_id']): ?>
                                    <small class="badge">(Registered Administrator)</small>
                                <?php endif; ?>
                            </span>
                            <span class="feedback-time">🕒 <?php echo $row['created_at']; ?></span>
                        </div>
                        <p class="feedback-text"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No feedback entries submitted yet. Be the first to share your thoughts!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
