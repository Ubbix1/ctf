<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XSS Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php">Back to Challenges</a>
    </div>
    <h1>XSS Challenge</h1>

    <div class="playground-container">
        <p>
            Find the XSS vulnerability in the following snippet: 
            <pre>&lt;input type="text" name="input" value="&lt;?php echo $_GET['input']; ?&gt;"&gt;</pre>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Describe the vulnerability" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = strtolower(trim($_POST['answer']));

            if ($answer === "unsanitized input allows script injection") {
                echo '<div class="result correct">Correct! Flag: flag{plexaur_xss_caught}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
