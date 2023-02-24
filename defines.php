<?php
declare(strict_types=1);

/*
 * 420DW3_Integration_Project_Demo defines.php
 * 
 * @author Marc-Eric Boury (MEbou)
 * @since 2/9/2023
 * (c) Copyright 2023 Marc-Eric Boury 
 */


// ABSOLUTE (INTERNAL) PATHS
/**
 * Absolute path to the root of the project
 */
const PROJECT_ROOT = __DIR__.DIRECTORY_SEPARATOR;
/**
 * Absolute path to the private directory
 */
const PRIVATE_DIR = PROJECT_ROOT."private".DIRECTORY_SEPARATOR;
/**
 * Absolute path to the fragments directory
 */
const FRAGMENTS_DIR = PRIVATE_DIR."fragments".DIRECTORY_SEPARATOR;
/**
 * Absolute path to the includes directory
 */
const INCLUDES_DIR = PRIVATE_DIR."includes".DIRECTORY_SEPARATOR;
/**
 * Absolute path to the src directory
 */
const SOURCES_DIR = PRIVATE_DIR."src".DIRECTORY_SEPARATOR;


// SERVER-RELATIVE (WEB) PATHS
/**
 * Relative path (for the web) to the project root directory
 */
const WEB_ROOT = "/420DW3_Integration_Project_Demo/";
/**
 * Relative path (for the web) to the public directory
 */
const PUBLIC_DIR = WEB_ROOT."public/";
/**
 * Relative path (for the web) to the css directory
 */
const CSS_DIR = PUBLIC_DIR."css/";
/**
 * Relative path (for the web) to the images directory
 */
const IMAGES_DIR = PUBLIC_DIR."images/";
/**
 * Relative path (for the web) to the js directory
 */
const JS_DIR = PUBLIC_DIR."js/";
/**
 * Relative path (for the web) to the pages directory
 */
const PAGES_DIR = PUBLIC_DIR."pages/";




// Registering manual autoloader for object-oriented PHP
/**
 * @param string $class
 *
 * @return bool
 *
 * @author Marc-Eric Boury
 * @since  2/16/2023
 */
$psr4_autoloader = function(string $class) : bool {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
    $file_path = SOURCES_DIR . DIRECTORY_SEPARATOR . $file;
    if (file_exists($file_path)) {
        require $file_path;
        return true;
    }
    return false;
};

spl_autoload_register($psr4_autoloader);