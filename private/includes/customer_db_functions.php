<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo customer_db_functions.php
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
 * Retrieves a customer from the database based on its id number.
 *
 * @param int $id The id of the customer.
 *
 * @return array An associative array representing the customer.
 * @throws DatabaseLogicException
 * @throws ValidationException
 * @throws DatabaseConnectionException
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function get_customer_by_id(int $id) : array {
    db_validate_int_id($id, true);
    
    $sql = "SELECT * FROM `customers` WHERE `id` = $id;";
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
 * Creates a new customer in the database from the required username and password hash.
 *
 * @param string $username     The customer username
 * @param string $passwordHash The customer's hashed password
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 * @throws DatabaseLogicException
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function create_customer(string $username, string $passwordHash) : array {
    db_validate_string($username, true);
    db_validate_string($passwordHash, true);
    
    $sql = "INSERT INTO `customers` (`username`, `passwordHash`) VALUES (\"".$username."\", \"".$passwordHash."\")";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        $new_id = $connection->insert_id;
        return get_customer_by_id($new_id);
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
}

/**
 * @param array $customer_array
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function update_customer(array $customer_array) : array {
    db_validate_int_id($customer_array["id"], true);
    db_validate_string($customer_array["username"], true);
    db_validate_string($customer_array["passwordHash"], true);
    if (empty($customer_array["dateCreated"])) {
        throw new ValidationException("Invalid customer creation date: [".$customer_array["dateCreated"]."].");
    }
    
    $sql = "UPDATE `customers` SET `username` = \"".$customer_array["username"]."\",
    `passwordHash` = \"".$customer_array["passwordHash"]."\",
    `dateCreated` = \"".$customer_array["dateCreated"]."\"
    WHERE `id` = ".$customer_array["id"].";";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return $customer_array;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
}

/**
 * Deletes a re customer from the database
 *
 * @param int $id
 *
 * @return bool|array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function delete_customer(int $id) : bool|array {
    db_validate_int_id($id, true);
    
    $sql = "DELETE FROM `customers` WHERE `id` = $id;";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return true;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
    
}