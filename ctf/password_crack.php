<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Crack Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php">Back to Challenges</a>
    </div>
    <h1>Password Crack Challenge</h1>

    <div class="playground-container">
        <p>
            Crack the following hashed password (MD5): <strong>5f4dcc3b5aa765d61d8327deb882cf99</strong>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Enter the cracked password" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = strtolower(trim($_POST['answer']));

            if ($answer === "password") {
                echo '<div class="result correct">Correct! Flag: flag{plexaur_password_found}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
