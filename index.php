<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF - Plexaur.com</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="./assets/styles/style.css">
    <link rel="stylesheet" href="./assets/styles/navbar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./assets/styles/footer.css?v=<?php echo time(); ?>">
</head>
<body>
    
    <?php
        include "./components/navbar.php";
    ?>
    <main>
        <div class="content">
            <h1>let's begin with capturing the flag</h1>
            <a class="branding" href="https://plexaur.com">- Plexaur</a>
            <a class="getstart" href="#tools">get started</a>
        </div>
    </main>

    <section id="tools">
        <h1 class="heading">tools we provide</h1>
        <div class="card">
            <a href="./Base64/">
            <div class="card-child">
                <h1>base64</h3>
                <hr>
                <img src="./assets/images/arrow.svg" alt="">
            </div>
            </a>
            <a href="./steg/">
            <div class="card-child">
                <h1>steganography</h3>
                    <hr>
                <img src="./assets/images/arrow.svg" alt="">
            </div>
            </a>
            <a href="./color_invert/">
            <div class="card-child">
                <h1>Image color inverter</h1>
                <hr>
                <img src="./assets/images/arrow.svg" alt="">
            </div>
            </a>
            <a href="./ctf/">
            <div class="card-child">
                <h1>CTF</h1>
                <hr>
                <img src="./assets/images/arrow.svg" alt="">
            </div>
            </a>
        </div>
    </section>

    <section id="subscribe" class="subscribe">
        <h1 class="heading">subscribe to get the latest update.</h1>
        <form action="">
            <input type="email" name="" id="" placeholder="Enter your email">
            <input type="submit" value="submit">
        </form>
    </section>
    <?php 
        include "./components/footer.php";
    ?>
    <script src="./assets/script/script.js"></script>
</body>
</html>