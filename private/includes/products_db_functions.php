<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo products_db_functions.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */

require_once "../../defines.php";
require_once "database.php";

use Normslabs\WebApplication\System\Exceptions\DatabaseConnectionException;
use Normslabs\WebApplication\System\Exceptions\DatabaseLogicException;
use Normslabs\WebApplication\System\Validation\Exceptions\ValidationException;

/**
 * @param int $id
 *
 * @return array
 * @throws ValidationException
 * @throws DatabaseLogicException
 * @throws DatabaseConnectionException
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function get_product_by_id(int $id) : array {
    db_validate_int_id($id, true);
    $sql = "SELECT * FROM `products` WHERE `id` = $id;";
    $connection = db_get_connection();
    $result_set = $connection->query($sql);
    if ($result_set->num_rows == 0) {
        return [];
    } elseif ($result_set->num_rows > 1) {
        throw new DatabaseLogicException("Oh boy you have a problem, bro!");
    }
    return $result_set->fetch_assoc();
    
}