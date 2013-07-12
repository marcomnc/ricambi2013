<?php

define("MAGE_BASE_DIR", "..".DIRECTORY_SEPARATOR);
define("BASE_STORE", 0);

define("IT_STORE", 1);
define("EN_STORE", 2);
define("FR_STORE", 3);
define("DH_STORE", 4);
define("ES_STORE", 5);

require_once(MAGE_BASE_DIR.'app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . DIRECTORY_SEPARATOR ."error.log");

?>