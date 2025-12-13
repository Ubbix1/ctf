<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base64 Decode Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php" style="color:white">Back to Challenges</a>
    </div>
    <h1>Base64 Decode Challenge</h1>

    <div class="playground-container">
        <p>
            Decode the following Base64 encoded string: 
            <strong>U29sdmUgdGhpcyBjaGFsbGVuZ2U=</strong>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Enter the decoded text" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = strtolower(trim($_POST['answer']));

            if ($answer === "solve this challenge") {
                echo '<div class="result correct">Correct! Flag: flag{plexaur_decoded_successfully}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
