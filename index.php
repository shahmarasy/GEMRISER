<?php
/*
 * GEMRISER v0.5.3
 */

//Start the Sessionشسی
session_start();

// Defines
define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('APP_DIR', ROOT_DIR .'application/');

// Includes
require(APP_DIR .'config/config.php');
require(ROOT_DIR .'system/model.php');
require(ROOT_DIR .'system/view.php');
require(ROOT_DIR .'system/controller.php');
require(ROOT_DIR .'system/gemriser.php');

// Define base URL
global $config;
define('BASE_URL', $config['base_url']);

gemriser();

?>
