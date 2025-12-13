<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/styles/navbar.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="#">CTF Plexaur</a>
        </div>
        <nav>
            <ul id="deskNav">
                <li><a href="https://plexaur.com/php/login">Login</a></li>
				
                <li>
                    <a href="#">Tools</a> <!-- Dropdown trigger -->
                    <ul class="dropdown">
                        <li><a href="/Base64/">Base64</a></li>
                        <li><a href="/steg/">Steg</a></li>
                        <li><a href="/color_invert/">Invert</a></li>
                        <li><a href="/pcap-decoder.html">PCAP Decoder</a></li>
                        <li><a href="/cap-reader.html">CAP Reader</a></li>
                    </ul>
                </li>
				<li><a href="/ctf/index.php">CTF</a></li>
            </ul>
        </nav>
        <div id="toggle" class="toggle" style="display: none;">
            <div class="hamburger"></div>
            <div class="hamburger"></div>
            <div class="hamburger"></div>
        </div>
    </header>

    <div id="showNav" class="toggled" style="display: none;">
        <ul>
            <li><a href="">Login</a></li>
            <li><a href="">CTF</a></li>
            <li><a href="">Base64</a></li>
            <li><a href="">Steg</a></li>
            <li><a href="">Invert</a></li>
        </ul>
    </div>
</body>
</html>
