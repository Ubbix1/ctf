<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metadata Analysis Challenge</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the global style.css -->
</head>
<body>
	 <div class="nav">
        <a href="index.php">Back to Challenges</a>
    </div>
	
    <h1>Metadata Analysis Challenge</h1>

    <div class="playground-container">
        <p>
            Analyze the provided image file and find the hidden metadata. 
            <a href="sample_image.jpg" download>Download the image</a>
        </p>

        <form method="POST">
            <input type="text" name="answer" placeholder="Enter the hidden metadata" required>
            <button type="submit">Submit</button>
        </form>

        <?php
        if (isset($_POST['answer'])) {
            $answer = strtolower(trim($_POST['answer']));

            if ($answer === "flag{metadata_exposed}") {
                echo '<div class="result correct">Correct! Flag: flag{metadata_exposed}</div>';
            } else {
                echo '<div class="result incorrect">Incorrect! Try again.</div>';
            }
        }
        ?>
    </div>
</body>
</html>
