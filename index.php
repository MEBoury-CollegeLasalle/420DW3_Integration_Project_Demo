<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo index.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/9/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */


require_once __DIR__.DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."debug_functions.php";
require_once INCLUDES_DIR."database.php";
require_once INCLUDES_DIR."customer_db_functions.php";

?>
<!Doctype html>
<html lang="en-CA">
<head>
    <title>420DW3 - Integration Project</title>
    <link rel="shortcut icon" href="<?=WEB_ROOT."favicon.ico"?>">
    <link rel="stylesheet" href="<?=CSS_DIR."bootstrap.css"?>">
    <link rel="stylesheet" href="<?=CSS_DIR."main.css"?>">
    <link rel="stylesheet" href="<?=CSS_DIR."header.css"?>">
    <link rel="stylesheet" href="<?=CSS_DIR."footer.css"?>">
    <link rel="stylesheet" href="https://css.gg/css">
    <script src="<?=JS_DIR."bootstrap.bundle.js"?>"></script>
    <script src="<?=JS_DIR."jquery-3.6.3.js"?>"></script>
    <script src="<?=JS_DIR."main.js"?>"></script>
</head>
<body class="fullwidth">
<header class="header container-fluid">
    <?php
    require FRAGMENTS_DIR."standard.header.php";
    ?>
</header>
<main class="main container-fluid">
    <h1>index.php</h1>
    <?php
    debug($_SERVER);
    debug($_REQUEST);
    ?>
</main>
<footer class="footer container-fluid">
    <?php
    require FRAGMENTS_DIR."standard.footer.php";
    ?>
</footer>
</body>
</html>
