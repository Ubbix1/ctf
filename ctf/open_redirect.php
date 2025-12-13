<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Redirect Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php">Back to Challenges</a>
    </div>
    <h1>Open Redirect Challenge</h1>

    <div class="playground-container">
        <p>
            Find the open redirect vulnerability in the following URL: 
            <a href="https://example.com?redirect=http://malicious.com">Example.com</a>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Describe the vulnerability" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = strtolower(trim($_POST['answer']));

            if ($answer === "open redirect allows phishing") {
                echo '<div class="result correct">Correct! Flag: flag{plexaur_redirect_caught}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
