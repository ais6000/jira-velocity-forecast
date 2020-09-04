<?php
const ROOT = __DIR__ . '/../';
spl_autoload_register(function ($className) {
    include_once ROOT . str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php';
});
$main = new \Controller\Main();
$main->execute();
