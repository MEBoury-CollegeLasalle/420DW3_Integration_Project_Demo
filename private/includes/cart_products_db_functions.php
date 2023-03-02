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
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function get_cart_product_by_ids(int $cartId, int $productId) : array {
    
    $sql = "SELECT * FROM `cart_products` WHERE `cartId` = ? AND `productId` = ?;";
    try {
        db_validate_int_id($cartId, true);
        db_validate_int_id($productId, true);
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->prepare($sql);
        $statement->bind_param("ii", $cartId, $productId);
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
            "Failure to retrieve cart product association for cart id ".
            "# [$cartId] and product id # [$productId] from database.",
            0,
            $exception
        );
    }
}

/**
 * @param int $cartId
 * @param int $productId
 * @param int $quantity
 *
 * @return array
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function create_cart_product(int $cartId, int $productId, int $quantity = 1) : array {
    $sql = "INSERT INTO `cart_products` (`cartId`, `productId`, `quantity`) ".
           "VALUES (?, ?, ?);";
    try {
        db_validate_int_id($cartId, true);
        db_validate_int_id($productId, true);
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->bind_param("iii", $cartId, $productId, $quantity);
        $statement->execute();
        return get_cart_product_by_ids($cartId, $productId);
        
    } catch (Exception $exception) {
        throw new Exception(
            "Failure to insert cart product association for cart id ".
            "# [$cartId] and product id # [$productId] in database.",
            0,
            $exception
        );
    }
}

/**
 * @param array $cart_product
 *
 * @return array
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function update_cart_product(array $cart_product) : array {
    $sql = "UPDATE `cart_products` SET `quantity` = ? WHERE `cartId` = ? AND `productId` = ?;";
    
    try {
        db_validate_int_id($cart_product["cartId"], true);
        db_validate_int_id($cart_product["productId"], true);
        if (empty($cart_product["quantity"]) || !is_numeric($cart_product["quantity"])) {
            throw new ValidationException("Invalid cart product quantity: [".$cart_product["quantity"]."].");
        }
        if (empty($cart_product["dateAdded"])) {
            throw new ValidationException("Invalid cart product creation date: [".$cart_product["dateAdded"]."].");
        }
        
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->bind_param("iii",
                               $cart_product["quantity"],
                               $cart_product["cartId"],
                               $cart_product["productId"]);
        $statement->execute();
        return $cart_product;
    
    } catch (Exception $exception) {
        throw new Exception(
            "Failure to update cart product association for cart id # [".$cart_product["cartId"]."] ".
            "and product id # [".$cart_product["productId"]."] in database.",
            0,
            $exception
        );
    }
}

/**
 * @param int $cartId
 * @param int $productId
 *
 * @return array|true
 * @throws DatabaseConnectionException
 * @throws ValidationException
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function delete_cart_product(int $cartId, int $productId) : bool|array {
    $sql = "DELETE FROM `cart_products` WHERE `cartId` = ? AND `productId` = ?;";
    try {
        db_validate_int_id($cartId, true);
        db_validate_int_id($productId, true);
    
        $connection = db_get_connection();
        $statement = $connection->prepare($sql);
        $statement->bind_param("ii", $cartId, $productId);
        $statement->execute();
        return true;
    
    } catch (Exception $exception) {
        throw new Exception(
            "Failure to delete cart product association for cart id # [$cartId] ".
            "and product id # [$productId] from database.",
            0,
            $exception
        );
    }
    
}

/**
 * @param int $cartId
 * @param int $productId
 * @param int $quantity
 *
 * @return bool
 * @throws DatabaseConnectionException
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function add_product_to_cart(int $cartId, int $productId, int $quantity) : bool {
    $connection = db_get_connection();
    $connection->begin_transaction();
    
    try {
        $product_array = get_product_by_id($productId);
        if (empty($product_array)) {
            throw new Exception("Product id # [$productId] not found.");
        }
    
        // first, check available quantity
        if ($product_array["availableQty"] < $quantity) {
            throw new Exception("Cannot add [$quantity] ".$product_array["displayName"]." to cart: not enough units of the product are available.");
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
        
        $connection->commit();
    
        return true;
        
    } catch (Exception $exception) {
        $connection->rollback();
        throw new Exception("Failed to add product id # [$productId] to cart id #".
                            " [$cartId] with quantity [$quantity].", 0, $exception);
    }
    
}

/**
 * @param int $cartId
 * @param int $productId
 * @param int $quantity
 *
 * @return bool
 * @throws DatabaseConnectionException
 * @throws Exception
 *
 * @author Marc-Eric Boury
 * @since  2023-03-02
 */
function remove_product_from_cart(int $cartId, int $productId, int $quantity) : bool {
    $connection = db_get_connection();
    $connection->begin_transaction();
    
    try {
        $product_array = get_product_by_id($productId);
        if (empty($product_array)) {
            throw new Exception("Product id # [$productId] not found.");
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
    
        $connection->commit();
        
        return true;
        
    } catch (Exception $exception) {
        $connection->rollback();
        throw new Exception("Failed to remove quantity [$quantity] of product id # [$productId] ".
                            "from cart id # [$cartId].", 0, $exception);
    }
    
}