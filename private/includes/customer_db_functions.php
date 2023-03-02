<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo customer_db_functions.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."database.php";

use Normslabs\WebApplication\System\Exceptions\DatabaseLogicException;
use Normslabs\WebApplication\System\Validation\Exceptions\ValidationException;


/**
 * Retrieves a customer from the database based on its id number.
 *
 * @param int $id The id of the customer.
 *
 * @return array An associative array representing the customer.
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function get_customer_by_id(int $id) : array {
    $sql = "SELECT * FROM `customers` WHERE `id` = ?;";
    try {
        db_validate_int_id($id, true);
        
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $id);
        $statement->execute();
        $result_set = $statement->get_result();
        if ($result_set->num_rows == 0) {
            return [];
        } elseif ($result_set->num_rows > 1) {
            throw new DatabaseLogicException("Oh boy you have a problem, bro!");
        }
        return $result_set->fetch_assoc();
        
    } catch (Exception $exception) {
        throw new Exception(
            "Failure to retrieve customer id # [$id] from database.",
            0,
            $exception
        );
    }
}

/**
 * Creates a new customer in the database from the required username and password hash.
 *
 * @param string $username     The customer username
 * @param string $passwordHash The customer's hashed password
 *
 * @return array
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function create_customer(string $username, string $passwordHash) : array {
    $sql = "INSERT INTO `customers` (`username`, `passwordHash`) VALUES (?, ?)";
    
    try {
        db_validate_string($username, true);
        db_validate_string($passwordHash, true);
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->bind_param("ss", $username, $passwordHash);
        $statement->execute();
        $new_id = $connection->insert_id;
        return get_customer_by_id($new_id);
        
    } catch (Exception $exception) {
        throw new Exception(
            "Failure to insert customer named [$username] in the database.",
            0,
            $exception
        );
    }
}

/**
 * @param array $customer_array
 *
 * @return array
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function update_customer(array $customer_array) : array {
    
    $sql = "UPDATE `customers` SET `username` = ?, `passwordHash` = ? WHERE `id` = ?;";
    try {
        db_validate_int_id($customer_array["id"], true);
        db_validate_string($customer_array["username"], true);
        db_validate_string($customer_array["passwordHash"], true);
        if (empty($customer_array["dateCreated"])) {
            throw new ValidationException("Invalid customer creation date: [".$customer_array["dateCreated"]."].");
        }
    
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->bind_param("ssi", $customer_array["username"],
                               $customer_array["passwordHash"],
                               $customer_array["id"]);
        $statement->execute();
        return $customer_array;
        
    } catch (Exception $exception) {
        throw new Exception(
            "Failure to update customer id # [".$customer_array["id"]."] in the database.",
            0,
            $exception
        );
    }
}

/**
 * Deletes a re customer from the database
 *
 * @param int $id
 *
 * @return bool|array
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function delete_customer(int $id) : bool|array {
    $sql = "DELETE FROM `customers` WHERE `id` = ?;";
    
    try {
        db_validate_int_id($id, true);
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->bind_param("i", $id);
        $statement->execute();
        return true;
        
    } catch (Exception $exception) {
        throw new Exception(
            "Failure to delete customer id # [".$id."] from the database.",
            0,
            $exception
        );
    }
    
}