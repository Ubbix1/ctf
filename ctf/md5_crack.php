<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MD5 Crack Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php">Back to Challenges</a>
    </div>
    <h1>MD5 Crack Challenge</h1>

    <div class="playground-container">
        <p>
            Crack the following MD5 hash: <strong>d8578edf8458ce06fbc5bb76a58c5ca4</strong>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Enter the cracked text" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = strtolower(trim($_POST['answer']));

            if ($answer === "qwerty") {
                echo '<div class="result correct">Correct! Flag: flag{plexaur_md5_qwerty_cracked}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
