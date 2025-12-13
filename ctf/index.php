<?php
session_start();

// Set session duration (1 hour = 3600 seconds)
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}

$duration = 3600; // 1 hour

// Check if session has expired
if (time() - $_SESSION['start_time'] > $duration) {
    // Reset attempts and session data
    session_unset();
    session_destroy();
    header("Location: index.php"); // Reload page
    exit();
}

// Initialize attempts if not already set
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = [
        'caesar_cipher' => 0,
        'metadata_analysis' => 0,
        'base64_decode' => 0,
        'open_redirect' => 0,
        'password_crack' => 0,
        'open_ports' => 0,
        'xss' => 0,
        'md5_crack' => 0
    ];
}

// Calculate remaining time for countdown
$remaining_time = $duration - (time() - $_SESSION['start_time']);
$hours = floor($remaining_time / 3600);
$minutes = floor(($remaining_time % 3600) / 60);
$seconds = $remaining_time % 60;

// Process flag submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['challenge'], $_POST['flag'])) {
    $challenge = $_POST['challenge'];
    $submitted_flag = strtolower(trim($_POST['flag']));
    
    $correct_flags = [
        'caesar_cipher' 		=> 'flag{plexaur_caesar}',
        'metadata_analysis' 	=> 'flag{plexaur_meta_data}',
        'base64_decode' 		=> 'flag{plexaur_decoded_successfully}',
        'open_redirect' 		=> 'flag{plexaur_redirect_caught}',
        'password_crack' 		=> 'flag{plexaur_password_found}',
        'open_ports' 			=> 'flag{plexaur_ports_opened}',
        'xss' 					=> 'flag{plexaur_xss_caught}',
        'md5_crack' 			=> 'flag{plexaur_md5_qwerty_cracked}'
    ];

    if ($_SESSION['attempts'][$challenge] < 3) {
        if (isset($correct_flags[$challenge]) && $submitted_flag === $correct_flags[$challenge]) {
            echo "<p class='success'>Correct! You solved the $challenge challenge.</p>";
        } else {
            $_SESSION['attempts'][$challenge]++;
            if ($_SESSION['attempts'][$challenge] >= 3) {
                echo "<p class='error'>Too many attempts! You are locked out of this challenge.</p>";
            } else {
                echo "<p class='error'>Incorrect flag. Attempts remaining: " . (3 - $_SESSION['attempts'][$challenge]) . "</p>";
            }
        }
    } else {
        echo "<p class='error'>You are locked out of this challenge.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Challenge Index</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to global style.css -->
    <script>
        function startTimer() {
            document.getElementById('challenges').style.display = 'grid'; // Show challenges after clicking start
            var countdownElement = document.getElementById('countdown');
            var timeLeft = <?= $remaining_time; ?>; // Time in seconds

            var timer = setInterval(function () {
                var hours = Math.floor(timeLeft / 3600);
                var minutes = Math.floor((timeLeft % 3600) / 60);
                var seconds = timeLeft % 60;

                // Format the timer as hr:mm:ss
                countdownElement.innerHTML = hours.toString().padStart(2, '0') + ':' +
                    minutes.toString().padStart(2, '0') + ':' +
                    seconds.toString().padStart(2, '0');

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    alert('CTF session expired!');
                    window.location.reload(); // Reload page to clear session
                }

                timeLeft--;
            }, 1000);
        }
    </script>
    <style>
		
		body {
			background-color:black;
		}
        .challenge-grid {
			width: 80%;
            display:flex !important;
			flex-direction: column !important;
			justify-content: center;
			align-items: center;
        }
        .challenge-item {
            background-color: rgba(0, 0, 0, 0.7); /* Glassmorphic effect */
            border-radius: 10px;
            padding: 20px;
            color: black;
            text-align: center;
            transition: transform 0.3s;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			gap: 10px;
			width: 400px;
        }
        .challenge-item:hover {
            transform: scale(1.05);
        }
        .timer-container {
            margin: 20px 0;
            font-size: 20px;
            color: white; /* Timer color */
        }
		
		.challenge-item a {
			color: white !important;
			font-size: 20px;
			text-transform: capitalize;
			font-style: sans-serif
		}
		
		form button { margin: 10px 0;}
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="color: white;">CTF Challenge</h1>
        <div class="timer-container">
            <span id="countdown"><?= sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds); ?></span>
        </div>
        <button onclick="startTimer()">Start CTF</button>
    </div>

    <div id="challenges" class="challenge-grid" style="display:none;">
        <h2>Select a Challenge and Submit Flag</h2>
        <div class="challenge-item">
            <a href="caesar_cipher.php">Caesar Cipher Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="caesar_cipher">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
        <div class="challenge-item">
            <a href="metadata_analysis.php">Metadata Analysis Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="metadata_analysis">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
        <div class="challenge-item">
            <a href="base64_decode.php">Base64 Decode Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="base64_decode">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
        <div class="challenge-item">
            <a href="open_redirect.php">Open Redirect Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="open_redirect">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
        <div class="challenge-item">
            <a href="password_crack.php">Password Crack Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="password_crack">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
        <div class="challenge-item">
            <a href="open_ports.php">Open Ports Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="open_ports">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
        <div class="challenge-item">
            <a href="xss.php">XSS Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="xss">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
        <div class="challenge-item">
            <a href="md5_crack.php">MD5 Crack Challenge</a>
            <form method="POST">
                <input type="hidden" name="challenge" value="md5_crack">
                <input type="text" name="flag" placeholder="Submit flag" required>
                <button type="submit">Submit Flag</button>
            </form>
        </div>
    </div>
</body>
</html>
