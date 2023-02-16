<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo database.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */


/**
 * Returns a new {@see mysqli} connection object.
 *
 * @return mysqli
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function get_connection() : mysqli {
    $mysqli = new mysqli("localhost", "root", "", "420dw3_project", 3306);
    return $mysqli;
}