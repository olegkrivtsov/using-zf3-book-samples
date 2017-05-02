<?php 
// login.php
session_start();

// If user is logged in, redirect him to index.php
if (isset($_SESSION['identity'])) {
    header('Location: index.php');
    exit;
}

// Check if form is submitted.
$submitted = false;
if ($_SERVER['REQUEST_METHOD']=='POST') {
    
    $submitted = true;
    
    // Extract form data.
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Authenticate user.
    $authenticated = false;
    if ($email=='admin@example.com' && $password=='Secur1ty') {
        $authenticated = true;
        
        // Save identity to session.
        $_SESSION['identity'] = $email;
        
        // Redirect the user to index.php.
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login page</title>
    </head>
    <body>
        <h1>Sign in</h1>
        <?php if ($submitted && !$authenticated): ?>
            <div class="alert">
                Invalid credentials.
            </div>
        <?php endif; ?>
        <form name="login-form" action="/login.php" method="POST">
            <label for="email">E-mail</label>
            <input type="text" name="email">
            <br>
            <label for="password">Password</label>
            <input type="password" name="password">
            <br>
            <input type="submit" name="submit" value="Sign In">
        </form>
    </body>
</html>