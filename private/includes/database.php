<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo database.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */

require_once "../../defines.php";

use Normslabs\WebApplication\System\Exceptions\DatabaseConnectionException;
use Normslabs\WebApplication\System\Validation\Exceptions\ValidationException;


/**
 * Returns a new {@see mysqli} connection object.
 *
 * @return mysqli
 *
 * @throws DatabaseConnectionException
 * @author   Marc-Eric Boury
 * @since    2/16/2023
 */
function db_get_connection() : mysqli {
    $mysqli = new mysqli("localhost", "root", "", "420dw3_project", 3306);
    if ($mysqli->errno) {
        throw new DatabaseConnectionException($mysqli->error, $mysqli->errno);
    }
    return $mysqli;
}


/**
 * Simple validation function for strings that are to be inserted in some way in the database.
 * Rejects strings that are empty or that contains any of the following characters:
 * <ul>
 * <li>? (question mark)</li>
 * <li>; (semicolon)</li>
 * <li>` (backtick)</li>
 * <li>' (single quote)</li>
 * <li>" (double quote)</li>
 * <li>( (opening parenthesis)</li>
 * <li>) (closing patenthesis)</li>
 * </ul>
 *
 * @param string $string The string to validate
 * @param bool   $throwExceptions Whether to throw an exception or not when validation fails
 *
 * @return bool
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function db_validate_string(string $string, bool $throwExceptions = false) : bool {
    $return_val = true;
    $matches = [];
    if (empty($string)) {
        $return_val = false;
        if ($throwExceptions) {
            throw new ValidationException(
                "String cannot be empty."
            );
        }
    } elseif (preg_match("/.*([?;`\"'()])+.*/mi", $string, $matches)) {
        $return_val = false;
        if ($throwExceptions) {
            throw new ValidationException(
                "Invalid character found in string: [".$matches[1]."]."
            );
        }
    }
    return $return_val;
}

/**
 * Simple validation function for database auto-incremented int IDs values. Accepts
 * integers or string representations of the ID.
 * Rejects the following values:
 * <ul>
 * <li>empty strings</li>
 * <li>Non-numeric strings</li>
 * <li>Integers or numeric strings that are inferior to the passed <code>$idAutoIncrementSeed</code>
 * parameter (default is 1)</li>
 * </ul>
 *
 * @param int|string $id_value The ID value to validate
 * @param bool       $throwExceptions Whether to throw an exception or not when validation fails
 * @param int        $idAutoIncrementSeed The int-value at wich the database auto-increment is set to start (default: 1)
 *
 * @return bool
 * @throws ValidationException
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
function db_validate_int_id(int|string $id_value, bool $throwExceptions = false, int $idAutoIncrementSeed = 1) : bool {
    if (is_string($id_value)) {
        if (empty($id_value)) {
            if ($throwExceptions) {
                throw new ValidationException("Id value is an empty string.");
            }
            return false;
        } elseif (!is_numeric($id_value)) {
            if ($throwExceptions) {
                throw new ValidationException("Id value is a non-numeric string.");
            }
            return false;
        }
        $id_value = (int) $id_value;
    }
    if ($id_value < $idAutoIncrementSeed) {
        if ($throwExceptions) {
            throw new ValidationException("Id value is inferior to the auto-increment seed ($id_value < $idAutoIncrementSeed).");
        }
        return false;
    }
    return true;
}