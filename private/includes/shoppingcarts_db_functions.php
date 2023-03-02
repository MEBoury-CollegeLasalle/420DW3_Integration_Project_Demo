<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo shoppingcarts_db_functions.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */

use Normslabs\WebApplication\System\Exceptions\DatabaseConnectionException;
use Normslabs\WebApplication\System\Exceptions\DatabaseLogicException;
use Normslabs\WebApplication\System\Validation\Exceptions\ValidationException;

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."database.php";


/**
 * @TODO   Documentation
 *
 * @author Marc-Eric Boury
 * @since  2023-03-01
 */
enum ShoppingCartStatus : string {
    case CREATED = "created";
    case ORDERED = "ordered";
}

/**
 * @param int $id
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws DatabaseLogicException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function get_shopcart_by_id(int $id) : array {
    db_validate_int_id($id, true);
    $sql = "SELECT * FROM `shopping_carts` WHERE `id` = $id;";
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
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 * @throws DatabaseLogicException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function create_shopcart() : array {
    
    $sql = "INSERT INTO `shopping_carts` (`status`) ".
           "VALUES (\"".ShoppingCartStatus::CREATED->value."\")";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        $new_id = $connection->insert_id;
        return get_shopcart_by_id($new_id);
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
}

/**
 * @param array $shopping_cart
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function update_shopcart(array $shopping_cart) : array {
    db_validate_int_id($shopping_cart["id"], true);
    db_validate_string($shopping_cart["status"], true);
    if (empty($shopping_cart["dateCreated"])) {
        throw new ValidationException("Invalid product creation date: [".$shopping_cart["dateCreated"]."].");
    }
    
    $sql = "UPDATE `shopping_carts` SET `status` = \"".$shopping_cart["status"]."\"
    WHERE `id` = ".$shopping_cart["id"].";";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return $shopping_cart;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];

}

/**
 * @param int $id
 *
 * @return bool|array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function delete_shopcart(int $id) : bool|array {
    db_validate_int_id($id, true);
    
    $sql = "DELETE FROM `shopping_carts` WHERE `id` = $id;";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return true;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];

}