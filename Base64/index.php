<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the inputText parameter is set
    if (isset($_POST['inputText'])) {
        $inputText = trim($_POST['inputText']);

        // Perform strict validation here if needed
        // For example, you might want to check if $inputText is not empty

        // If encoding is requested
        if (isset($_POST['action']) && $_POST['action'] === 'encode') {
            $encodedText = base64_encode($inputText);
            echo $encodedText;
        }

        // If decoding is requested
        elseif (isset($_POST['action']) && $_POST['action'] === 'decode') {
            $decodedText = base64_decode($inputText);
            echo $decodedText;
        }

        // Invalid action
        else {
            echo 'Invalid action.';
        }
    } else {
        echo 'InputText parameter is not set.';
    }
    exit; // Stop further execution after processing the request
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base64 Encoder and Decoder | plexaur</title>
    <meta property="og:image" content="https://ctf.plexaur.com/pictures/base64_image.jpg" />
    <meta name="keywords" content="plexaur, plexaur.com, ctf, ctf.plexaur, base64, encode, decode">
    <meta name="description" content="Encode and decode text and files in Base64 with Plexaur.com! Secure your data, explore tutorials, and discover innovative applications. Join our community and experience the power of Base64 encryption and decoding." />
    <meta property="og:title" content="Base64 Encryption & Decoding Services | Plexaur.com" />
    <meta property="og:description" content="Encode and decode text and files in Base64 with Plexaur.com! Secure your data, explore tutorials, and discover innovative applications. Join our community and experience the power of Base64 encryption and decoding." />
    <meta property="og:type" content="website" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Plexaur.com">
    <meta property="og:url" content="https://ctf.plexaur.com/base64/index.php">

    <link rel="stylesheet" href="../assets/styles/style.css">
    <link rel="stylesheet" href="../assets/styles/navbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/styles/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/styles/footer.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/styles/base64.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include "../components/navbar.php"; ?>

    <main>
        <div class="base">
            <h1 class="base_header">Base64 Encoder and Decoder</h1>
            <div class="input">
                <label for="">Enter Text:</label>
                <textarea id="inputText" class=""></textarea>
            </div>

            <div class="actbtn">
                    <button class="" type="button" onclick="performAction('encode')">Encode</button>
                    <button class="" type="button" onclick="performAction('decode')">Decode</button>
            </div>

            <div class="output">
                <strong>Result:</strong>
                <div id="outputText"></div>
                <button class="copy" onclick="copyToClipboard()">Copy Result</button>
            </div>
        </div>

        <!-- Copy Confirmation Box -->
        <div id="copyConfirmation">
            <div class="copy-confirmation-box">
                <p>Result copied to clipboard!</p>
            </div>
        </div>
    </main>

    <?php include "../components/footer.php"; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/script/script.js"></script>
    <script>
        function performAction(action) {
            var inputText = document.getElementById('inputText').value;
            $.post('index.php', {
                action: action,
                inputText: inputText
            }, function(data) {
                handleResult(data);
            }).fail(function() {
                handleResult('Error occurred while ' + (action === 'encode' ? 'encoding' : 'decoding') + '.');
            });
        }

        function handleResult(data) {
            var outputText = document.getElementById('outputText');
            outputText.innerHTML = data;

        }

        function showCopyConfirmation() {
            var copyConfirmation = document.getElementById('copyConfirmation');
            copyConfirmation.style.visibility = 'visible';

            // Hide the confirmation box after 3 seconds
            setTimeout(function() {
                copyConfirmation.style.visibility = 'hidden';
            }, 3000);
        }

        function copyToClipboard() {
            var outputText = document.getElementById('outputText');
            var textArea = document.createElement("textarea");
            textArea.value = outputText.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            // Handle the result and show confirmation
            showCopyConfirmation();
        }
    </script>
    <?php include "../components/footer.php"; ?>
</body>

</html>