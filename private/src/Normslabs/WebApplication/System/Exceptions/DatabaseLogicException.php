<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo DatabaseLogicException.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/16/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */

namespace Normslabs\WebApplication\System\Exceptions;

use Exception;
use Throwable;

/**
 * @TODO   Documentation
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
class DatabaseLogicException extends Exception {
    
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string         $message  [optional] The Exception message to throw.
     * @param int            $code     [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}