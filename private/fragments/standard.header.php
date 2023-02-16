<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo standard.header.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/9/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */


?>
<div class="title-bar row justify-content-center">
    <div class="title-container col-8">
        <div class="title">This is my titlebar</div>
    </div>
</div>
<div class="menu-bar row">
    <div class="nav-menu col-10 row justify-content-start">
        <div class="nav-menu-item">
            <a class="fill-div" href="<?=PAGES_DIR."/login.php"?>"><i class="icon gg-user"></i>&nbsp;Login</a>
        </div>
        <div class="nav-menu-item">
            <a class="fill-div" href="<?=PAGES_DIR."/products.php"?>"><i class="icon gg-box"></i>&nbsp;Shop</a>
        </div>
        <div class="nav-menu-item">
            <a class="fill-div" href="<?=PAGES_DIR."/cart.php"?>"><i class="icon gg-shopping-cart"></i>&nbsp;Cart</a>
        </div>
    </div>
    <div class="usermenu col-2 row justify-content-end">
    
    </div>
</div>
