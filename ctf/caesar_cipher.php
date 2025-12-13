<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caesar Cipher Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php" style="color:white">Back to Challenges</a>
    </div>
    <h1>Caesar Cipher Challenge</h1>

    <div class="playground-container">
        <p>
            Decode the following Caesar Cipher (shift 3): 
            <strong>Vkrqh lv dq dgydqfhg irup ri qrwklqj.</strong>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Enter the decoded text" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = strtolower(trim($_POST['answer']));

            if ($answer === "stone is an advanced form of nothing") {
                echo '<div class="result correct">Correct! Flag: flag{plexaur_caesar}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
