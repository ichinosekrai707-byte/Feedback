<?php
// ====================================================================
// FILE: login.php
// ====================================================================
session_start();
include "db.php";

// Redirect to dashboard immediately if the session is already authenticated
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Process data transactions sent via HTTP POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Test Case 1 Validation: Reject empty or blank inputs
    if (empty($username) || empty($password)) {
        $_SESSION['msg'] = "Username and password fields cannot be left blank.";
        $_SESSION['msg_type'] = "warning";
    } else {
        // Implement defensive prepared statements infrastructure for authentication lookups
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Hash the runtime payload password using SHA-256 matching database seed initialization configuration rules
            $hashed_input = hash('sha256', $password);

            // Test Case 3 Validation: Grant authorization token keys on valid match
            if ($hashed_input === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['msg'] = "Login successful! Welcome back, Admin.";
                $_SESSION['msg_type'] = "success";
                
                $stmt->close();
                $conn->close();
                header("Location: index.php");
                exit();
            } else {
                // Test Case 2 Validation: Invalid credential mismatch routing logic
                $_SESSION['msg'] = "Invalid username or password credentials.";
                $_SESSION['msg_type'] = "error";
            }
        } else {
            $_SESSION['msg'] = "Invalid username or password credentials.";
            $_SESSION['msg_type'] = "error";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Mini Feedback System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 450px; margin: 80px auto;">
        <div class="card">
            <h2>Administrative Sign-In</h2>
            <p class="subtitle" style="margin-bottom: 20px;">Please enter your authentication credentials to authenticate</p>

            <!-- LOCAL STATUS NOTIFICATION DISPATCH INLINE CONTEXT AREA -->
            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert <?php echo $_SESSION['msg_type']; ?>">
                    <?php 
                        echo $_SESSION['msg']; 
                        unset($_SESSION['msg']);
                        unset($_SESSION['msg_type']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                <div class="form-group" style="display: flex; flex-direction: column;">
                    <label for="username" style="margin-bottom: 5px; font-weight: bold;">Username</label>
                    <input type="text" id="username" name="username" placeholder="e.g. admin" style="width: 100%;">
                </div>
                
                <div class="form-group" style="display: flex; flex-direction: column; margin-bottom: 10px;">
                    <label for="password" style="margin-bottom: 5px; font-weight: bold;">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" style="width: 100%;">
                </div>

                <div style="display: flex; gap: 10px; align-items: center; width: 100%;">
                    <button type="submit" class="btn submit-btn" style="flex: 1; background-color: #0d6efd;">Sign In</button>
                    <a href="index.php" class="btn cancel-btn" style="flex: 1; background-color: #6c757d; text-align: center; text-decoration: none; color: white; line-height: normal; padding: 12px 16px;">Back to Feed</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
