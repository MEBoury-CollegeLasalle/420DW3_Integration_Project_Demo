<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo customer_db_functions.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */


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
    if ($id < 1) {
        throw new Exception("Invalid Id value: [$id].");
    }
    
    $sql = "SELECT * FROM customers WHERE id = $id;";
    $connection = get_connection();
    $result_set = $connection->query($sql);
    if ($result_set->num_rows == 0) {
        return [];
    } elseif ($result_set->num_rows > 1) {
        throw new Exception("Oh boy you have a problem, bro!");
    }
    return $result_set->fetch_assoc();
}

/**
 * Creates a new customer in the database from the required username and password hash.
 *
 * @param string $username The customer username
 * @param string $passwordHash The customer's hashed password
 *
 * @return array
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function create_customer(string $username, string $passwordHash) : array {
    if (empty($username) || empty($passwordHash)) {
        throw new Exception("Username or password hash cannot be empty.");
    }
    
    $sql = "INSERT INTO `customers` (`username`, `passwordHash`) VALUES (\"".$username."\", \"".$passwordHash."\")";
    $connection = get_connection();
    if ($connection->query($sql)) {
        $newId = $connection->insert_id;
        return get_customer_by_id($newId);
    } else {
        return [
            "errno" => $connection->errno,
            "error" => $connection->error
        ];
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
    if (!isset($customer_array["id"]) || !is_numeric($customer_array["id"]) || $customer_array["id"] < 1) {
        throw new Exception("Invalid customer id: [".$customer_array["id"]."].");
    } elseif (!isset($customer_array["username"]) || empty($customer_array["username"])) {
        throw new Exception("Invalid customer username: [".$customer_array["username"]."].");
    } elseif (!isset($customer_array["passwordHash"]) || empty($customer_array["passwordHash"])) {
        throw new Exception("Invalid customer passwordHash: [".$customer_array["passwordHash"]."].");
    } elseif (!isset($customer_array["dateCreated"]) || empty($customer_array["dateCreated"])) {
        throw new Exception("Invalid customer creation date: [".$customer_array["dateCreated"]."].");
    }
    
    $sql = "UPDATE `customers` SET `username` = \"".$customer_array["username"]."\",
    `passwordHash` = \"".$customer_array["passwordHash"]."\",
    `dateCreated` = \"".$customer_array["dateCreated"]."\"
    WHERE `id` = ".$customer_array["id"].";";
    $connection = get_connection();
    if ($connection->query($sql)) {
        return $customer_array;
    } else {
        return [
            "errno" => $connection->errno,
            "error" => $connection->error
        ];
    }
}

/**
 *
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
    if ($id < 1) {
        throw new Exception("Invalid Id value: [$id].");
    }
    $sql = "DELETE FROM customers WHERE id = $id;";
    $connection = get_connection();
    if ($connection->query($sql)) {
        return true;
    } else {
        return [
            "errno" => $connection->errno,
            "error" => $connection->error
        ];
    }
    
}