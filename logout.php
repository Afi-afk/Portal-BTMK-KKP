<?php
// 1. Initialize the session to access existing session data
session_start();

// 2. Clear all session variables stored in memory
$_SESSION = array();

// 3. Destroy the session cookie in the user's browser (if it exists)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // Set expiration time far in the past
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. Completely destroy the session on the server
session_destroy();

// 5. Redirect the user back to the login page with a success message flag
header("Location: login.php?status=logged_out");
exit();
?>
