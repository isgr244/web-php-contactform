<?php

session_start();

define("BASE_PATH", __DIR__);

require BASE_PATH . "/controllers/RootController.php";

$router = new RootController();

$router->route();
