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
const PROJECT_ROOT = __DIR__;
/**
 * Absolute path to the private directory
 */
const PRIVATE_DIR = PROJECT_ROOT."/private";
/**
 * Absolute path to the fragments directory
 */
const FRAGMENTS_DIR = PRIVATE_DIR."/fragments";
/**
 * Absolute path to the includes directory
 */
const INCLUDES_DIR = PRIVATE_DIR."/includes";
/**
 * Absolute path to the src directory
 */
const SOURCES_DIR = PRIVATE_DIR."/src";


// SERVER-RELATIVE (WEB) PATHS

/**
 * Relative path (for the web) to the project root directory
 */
const WEB_ROOT = "/420DW3_Integration_Project_Demo";
/**
 * Relative path (for the web) to the public directory
 */
const PUBLIC_DIR = WEB_ROOT."/public";
/**
 * Relative path (for the web) to the css directory
 */
const CSS_DIR = PUBLIC_DIR."/css";
/**
 * Relative path (for the web) to the images directory
 */
const IMAGES_DIR = PUBLIC_DIR."/images";
/**
 * Relative path (for the web) to the js directory
 */
const JS_DIR = PUBLIC_DIR."/js";
/**
 * Relative path (for the web) to the pages directory
 */
const PAGES_DIR = PUBLIC_DIR."/pages";
