<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo standard.footer.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-02-15
 * (c) Copyright 2023 Marc-Eric Boury 
 */

$now = new DateTime();
$year = $now->format("Y");

?>
<div class="footerbar row justify-content-evenly">
    <div class="footer-menu-container col-4">
    </div>
    <div class="footer-menu-container col-4">
    </div>
    <div class="footer-menu-container col-4">
    </div>
</div>
<div class="copyright-bar row justify-content-center">
    <div class="copyright-notice col-10">
        © <?= ($year > 2023) ? "2023-$year" : "2023" ?> Marc-Eric Boury - All rights reserved
    </div>
</div>
