<?php
// sql_injection.php
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Simulate SQL injection vulnerability
    if ($username === "admin' --" && $password === "") {
        echo "flag{admin}";
    } else {
        echo "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SQL Injection Challenge</title>
</head>
<body>
	 <div class="nav">
        <a href="index.php">Back to Challenges</a>
    </div>
    <h1>SQL Injection Challenge</h1>
    <form method="POST">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button type="submit" name="submit">Login</button>
    </form>
</body>
</html>
