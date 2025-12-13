<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Ports Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php">Back to Challenges</a>
    </div>
    <h1>Open Ports Challenge</h1>

    <div class="playground-container">
        <p>
            Identify the vulnerable port that often exposes MySQL databases: <strong>?</strong>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Enter the port number" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = trim($_POST['answer']);

            if ($answer === "3306") {
                echo '<div class="result correct">Correct! Flag:flag{plexaur_ports_opened}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
