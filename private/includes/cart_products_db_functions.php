<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo cart_products_db_functions.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-03-02
 * (c) Copyright 2023 Marc-Eric Boury 
 */

use Normslabs\WebApplication\System\Exceptions\DatabaseConnectionException;
use Normslabs\WebApplication\System\Exceptions\DatabaseLogicException;
use Normslabs\WebApplication\System\Validation\Exceptions\ValidationException;

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."defines.php";
require_once INCLUDES_DIR."database.php";
require_once INCLUDES_DIR."products_db_functions.php";
require_once INCLUDES_DIR."shoppingcarts_db_functions.php";

/**
 * @param int $cartId
 * @param int $productId
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 * @throws DatabaseLogicException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function get_cart_product_by_ids(int $cartId, int $productId) : array {
    db_validate_int_id($cartId, true);
    db_validate_int_id($productId, true);
    
    $sql = "SELECT * FROM `cart_products` WHERE `cartId` = $cartId AND `productId` = $productId;";
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
 * @param int $cartId
 * @param int $productId
 * @param int $quantity
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws DatabaseLogicException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function create_cart_product(int $cartId, int $productId, int $quantity = 1) : array {
    $sql = "INSERT INTO `cart_products` (`cartId`, `productId`, `quantity`) ".
           "VALUES ($cartId, $productId, $quantity);";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return get_cart_product_by_ids($cartId, $productId);
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
}

/**
 * @param array $cart_product
 *
 * @return array
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function update_cart_product(array $cart_product) : array {
    db_validate_int_id($cart_product["cartId"], true);
    db_validate_int_id($cart_product["productId"], true);
    if (empty($cart_product["quantity"]) || !is_numeric($cart_product["quantity"]))
    if (empty($cart_product["dateAdded"])) {
        throw new ValidationException("Invalid cart product creation date: [".$cart_product["dateAdded"]."].");
    }
    
    $sql = "UPDATE `cart_products` SET `quantity` = ".$cart_product["quantity"].",
    WHERE `cartId` = ".$cart_product["cartId"]." AND `productId` = ".$cart_product["productId"].";";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return $cart_product;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
}

/**
 * @param int $cartId
 * @param int $productId
 *
 * @return array|true
 * @throws DatabaseConnectionException
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function delete_cart_product(int $cartId, int $productId) : bool|array {
    db_validate_int_id($cartId, true);
    db_validate_int_id($productId, true);
    
    $sql = "DELETE FROM `cart_products` WHERE `cartId` = $cartId AND `productId` = $productId;";
    $connection = db_get_connection();
    if ($connection->query($sql)) {
        return true;
    }
    return [
        "errno" => $connection->errno,
        "error" => $connection->error
    ];
    
}

/**
 * @param int $cartId
 * @param int $productId
 * @param int $quantity
 *
 * @return bool
 * @throws DatabaseConnectionException
 * @throws DatabaseLogicException
 * @throws ValidationException
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function add_product_to_cart(int $cartId, int $productId, int $quantity) : bool {
    // TODO: Note, make a transaction here
    
    db_validate_int_id($productId);
    $product_array = get_product_by_id($productId);
    if (empty($product_array)) {
        throw new Exception("Product id # [$productId] not found.");
    }
    
    // TODO: replace fetch-checks by try-catch on insertion
    db_validate_int_id($cartId);
    $cart_array = get_shopcart_by_id($cartId);
    if (empty($cart_array)) {
        throw new Exception("Shopping cart id # [$cartId] not found.");
    }
    
    // first, check available quantity
    if ($product_array["availableQty"] < $quantity) {
        throw new Exception("Cannot add [$quantity] ".$product_array["displayName"]." to cart: not enough units of the product available.");
    }
    
    // then substract from the product's available quantity and update in database
    $product_array["availableQty"] -= $quantity;
    update_product($product_array);
    
    // finally, check if the item is already in the cart. if so we need only to change the quantity, otherwise, we need to create it
    $cart_product = get_cart_product_by_ids($cartId, $productId);
    if (empty($cart_product)) {
        // product is not in cart, create the link
        create_cart_product($cartId, $productId, $quantity);
        
    } else {
        // product is in the cart, modify its quantity
        $cart_product["quantity"] += $quantity;
        update_cart_product($cart_product);
        
    }
    
    return true;
    
}

/**
 * @param int $cartId
 * @param int $productId
 * @param int $quantity
 *
 * @return bool
 * @throws DatabaseConnectionException
 * @throws DatabaseLogicException
 * @throws ValidationException
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function remove_product_to_cart(int $cartId, int $productId, int $quantity) : bool {
    // TODO: Note, make a transaction here
    
    // TODO: replace fetch-checks by try-catch on insertion (foreign key checks with exception throw)
    db_validate_int_id($productId);
    $product_array = get_product_by_id($productId);
    if (empty($product_array)) {
        throw new Exception("Product id # [$productId] not found.");
    }
    db_validate_int_id($cartId);
    $cart_array = get_shopcart_by_id($cartId);
    if (empty($cart_array)) {
        throw new Exception("Shopping cart id # [$cartId] not found.");
    }
    
    // first, check that the product is actually in the cart.
    $cart_product = get_cart_product_by_ids($cartId, $productId);
    if (empty($cart_product)) {
        // product is not in cart
        throw new Exception("Product [".$product_array["displayName"]."] is not present in cart id # [$cartId].");
        
    }
    
    // prepare a variable to get the quantity of the product to re-add as available. this must be done before
    // changing the cart_product to handle the case where the quantity to remove is bigger than the one
    // in the cart
    $quantity_to_re_add = $quantity;
    
    // then check if the quantity in the cart is higher than the quantity to remove
    if ($cart_product["quantity"] > $quantity) {
        // there is more of the product in the cart, theres is gonna be some left, update the cart_product
        $cart_product["quantity"] -= $quantity;
        update_cart_product($cart_product);
        
    } else {
        // Set the product quantity to re-add as available as what was left in the cart.
        $quantity_to_re_add = $cart_product["quantity"];
        
        // remove more or equal of the quantity: remove the cart_product association altoghether from the DB
        delete_cart_product($cartId, $productId);
        
    }
    
    // finally re-add the quantity remove from the cart to the product's available quantity
    $product_array["availableQty"] += $quantity_to_re_add;
    update_product($product_array);
    
    return true;
    
}