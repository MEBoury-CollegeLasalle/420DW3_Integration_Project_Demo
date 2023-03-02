<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo orders_db_functions.php
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
enum OrderStatus : string {
    case CREATED = "created";
    case PLACED = "placed";
    case COMPLETED = "completed";
    case CANCELLED = "cancelled";
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
 * @since  3/2/2023
 */
function get_order_by_id(int $id) : array {
    db_validate_int_id($id, true);
    $sql = "SELECT * FROM `orders` WHERE `id` = $id;";
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
 * @param int    $customerId
 * @param int    $cartId
 * @param string $billingAddress
 * @param string $shippingAddress
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws DatabaseLogicException
 * @throws ValidationException
 * @author Marc-Eric Boury
 * @since  2023-03-01
 */
function create_order(int $customerId, int $cartId, string $billingAddress, string $shippingAddress) : array {
    db_validate_int_id($customerId, true);
    db_validate_int_id($cartId, true);
    db_validate_string($billingAddress, true);
    db_validate_string($shippingAddress, true);
    
    $sql = "INSERT INTO `orders` (`status`, `customerId`, `cartId`, `billingAddress`, `shippingAddress`) ".
           "VALUES (\"".OrderStatus::CREATED->value."\", ".$customerId.", ".$cartId.", \"".$billingAddress."\", \"".$shippingAddress."\")";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        $new_id = $connection->insert_id;
        return get_order_by_id($new_id);
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];

}

/**
 * @param array $order_array
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-01
 */
function update_order(array $order_array) : array {
    db_validate_int_id($order_array["id"], true);
    db_validate_string($order_array["status"], true);
    db_validate_int_id($order_array["customerId"], true);
    db_validate_int_id($order_array["cartId"], true);
    db_validate_string($order_array["billingAddress"], true);
    db_validate_string($order_array["shippingAddress"], true);
    if (empty($order_array["dateCreated"])) {
        throw new ValidationException("Invalid order creation date: [".$order_array["dateCreated"]."].");
    }
    
    $sql = "UPDATE `products` SET `status` = \"".$order_array["status"]."\",
    `customerId` = ".$order_array["customerId"].",
    `cartId` = ".$order_array["cartId"].",
    `billingAddress` = \"".$order_array["billingAddress"]."\",
    `shippingAddress` = \"".$order_array["shippingAddress"]."\"
    WHERE `id` = ".$order_array["id"].";";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return $order_array;
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
 * @since  2023-03-01
 */
function delete_order(int $id) : bool|array {
    db_validate_int_id($id, true);
    
    $sql = "DELETE FROM `orders` WHERE `id` = $id;";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return true;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];

}