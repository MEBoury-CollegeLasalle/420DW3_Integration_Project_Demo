<?php

/*
 * exercises1 debug_functions.php
 * 
 * @author Marc-Eric Boury (Newironsides)
 * @since 2023-01-05
 * (c) Copyright 2023 Marc-Eric Boury 
 */

declare(strict_types=1);

/**
 * Instructs {@see debug()} to echo its results to the output.
 */
const DEBUG_OUTPUT_ECHO = 1;
/**
 * Instructs {@see debug()} to echo its results to the output AND terminate the application.
 */
const DEBUG_OUTPUT_ECHO_AND_DIE = 2;
/**
 * Instructs {@see debug()} to return its results instead of adding to the output.
 */
const DEBUG_OUTPUT_RETURN = 3;

/**
 * Basic debug helper function. Generates an HTML table string for whatever value is provided in <code>$input</code>.
 * The table will contain the type data of the <code>$input</code> value, and its value(s). For container-types values
 * (arrays, objects...), the function is recursive and will display each element or property of the container-type.
 *
 * By default, the string is echoed before the function returns it.
 *
 * @param mixed $input        The value to debug
 * @param int   $outputMethod OPTIONAL: Whether to echo the generated HTML table string before returning it or not.
 *                            Defaults to <code>DEBUG_OUTPUT_ECHO</code>
 *
 * @return string|null
 *
 * @author Marc-Eric Boury
 * @since  2023-01-05
 */
function debug(mixed $input, int $outputMethod = DEBUG_OUTPUT_ECHO) : ?string {
    $return_value = "<table style='border: 1px solid black; border-collapse: collapse;'>";
    $input_type = gettype($input);
    switch ($input_type) {
        case "boolean":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>".($input ? "true" : "false")."</td></tr>";
            break;
        case "integer":
        case "double":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>$input</td></tr>";
            break;
        case "string":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'><pre>\"$input\"</pre></td></tr>";
            break;
        case "NULL":
            $return_value .= "<tr><td style='border: 1px solid black;'>null</td></tr>";
            break;
        case "array":
            $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'><table style='border: 1px solid black; border-collapse: collapse;'>";
            foreach ($input as $key => $value) {
                $key_name = $key;
                if (!is_numeric($key)) {
                    $key_name = "\"$key\"";
                }
                $return_value .= "<tr><td style='border: 1px solid black;'>$key_name</td><td style='border: 1px solid black;'>".
                              debug($value, DEBUG_OUTPUT_RETURN)."</td>";
            }
            $return_value .= "</table></td></tr>";
            break;
        case "object":
            try {
                $reflection_class = new ReflectionClass($input);
                $return_value .= "<tr><td style='border: 1px solid black;'>".$reflection_class->getShortName().
                              "</td><td style='border: 1px solid black;'><table style='border: 1px solid black; border-collapse: collapse;'>";
                $properties = $reflection_class->getProperties();
                foreach ($properties as $property) {
                    $return_value .= "<tr><td style='border: 1px solid black;'>\"".$property->getName().
                                  "\"</td><td style='border: 1px solid black;'>".
                                  debug($property->getValue($input), DEBUG_OUTPUT_RETURN)."</td>";
                }
                $return_value .= "</table></td></tr>";
            } catch (ReflectionException $refl_ex) {
                $return_value .= "<tr><td style='border: 1px solid black;'>ReflectionException thrown: ".
                              $refl_ex->getMessage()."</td></tr>";
            }
            break;
        case "resource":
        case "resource (closed)":
        case "unknown type":
        default:
            try {
                $return_value .= "<tr><td style='border: 1px solid black;'>$input_type</td><td style='border: 1px solid black;'>$input</td></tr>";
            } catch (Exception $exception) {
                $return_value .= "<tr><td style='border: 1px solid black;'>unstringifyable $input_type</td></tr>";
            }
            break;
    }
    $return_value .= "</table>";
    switch ($outputMethod) {
        case DEBUG_OUTPUT_RETURN:
            return $return_value;
        case DEBUG_OUTPUT_ECHO_AND_DIE:
            echo $return_value;
            die(0);
        case DEBUG_OUTPUT_ECHO:
        default:
            echo $return_value;
            return null;
    }
}

/**
 * Returns an array containing all the received request HTTP headers
 *
 * @return array
 *
 * @author Marc-Eric Boury
 * @since  2/2/2023
 */
function get_request_headers() : array {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}