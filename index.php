<?php
spl_autoload_register(function($className) {
	include_once str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php';
});
require_once 'Controller/Main.php';
