<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo products_db_functions.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."database.php";

use Normslabs\WebApplication\System\Exceptions\DatabaseConnectionException;
use Normslabs\WebApplication\System\Exceptions\DatabaseLogicException;
use Normslabs\WebApplication\System\Validation\Exceptions\ValidationException;

/**
 * @param int $id
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws DatabaseLogicException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-01
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

/**
 * Creates a new product in the database.
 *
 * @param string $displayName
 * @param string $description
 * @param string $imageUrl
 * @param float  $unitPrice
 * @param int    $availableQty
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws DatabaseLogicException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-01
 */
function create_product(string $displayName, string $description = "", string $imageUrl = "", float $unitPrice = 0.0, int $availableQty = 0) : array {
    db_validate_string($displayName, true);
    db_validate_string($description, true);
    
    $sql = "INSERT INTO `products` (`displayName`, `description`, `imageUrl`, `unitPrice`, `availableQty`) ".
           "VALUES (\"".$displayName."\", \"".$description."\", \"".$imageUrl."\", "+number_format($unitPrice, 2, ".", "")+", $availableQty)";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        $new_id = $connection->insert_id;
        return get_product_by_id($new_id);
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
}

/**
 * @param array $product_array
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-01
 */
function update_product(array $product_array) : array {
    db_validate_int_id($product_array["id"], true);
    db_validate_string($product_array["displayName"], true);
    db_validate_string($product_array["description"], true);
    if (empty($product_array["unitPrice"]) || !is_numeric($product_array["unitPrice"])) {
        throw new ValidationException("Invalid product unit price: [".$product_array["unitPrice"]."].");
    }
    if (empty($product_array["availableQty"]) || !is_numeric($product_array["availableQty"])) {
        throw new ValidationException("Invalid product available quantity: [".$product_array["availableQty"]."].");
    }
    if (empty($product_array["dateCreated"])) {
        throw new ValidationException("Invalid product creation date: [".$product_array["dateCreated"]."].");
    }
    
    $sql = "UPDATE `products` SET `displayName` = \"".$product_array["displayName"]."\",
    `description` = \"".$product_array["description"]."\",
    `unitPrice` = ".$product_array["unitPrice"].",
    `availableQty` = ".$product_array["availableQty"]."
    WHERE `id` = ".$product_array["id"].";";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return $product_array;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
}

/**
 * Deletes a product from the database
 *
 * @param int $id
 *
 * @return bool|array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-01
 */
function delete_product(int $id) : bool|array {
    db_validate_int_id($id, true);
    
    $sql = "DELETE FROM `products` WHERE `id` = $id;";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return true;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
    
}
