<?php

class RootController
{
    private static $routes = [

        "/" => [
            "controller" => "ContactController",
            "action" => "form"
        ],

        "/contact" => [
            "controller" => "ContactController",
            "action" => "form"
        ],

        "/contact/confirm" => [
            "controller" => "ContactController",
            "action" => "confirm"
        ],

        "/contact/complete" => [
            "controller" => "ContactController",
            "action" => "complete"
        ]

    ];

    public function route()
    {
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $path = rtrim($path, "/");
        if ($path === "") {
            $path = "/";
        }

        if (!isset(self::$routes[$path])) {
            require BASE_PATH . "/notfound.php";
            return;
        }

        $route = self::$routes[$path];

        $controllerName = $route["controller"];
        $action = $route["action"];
        require BASE_PATH . "/controllers/$controllerName.php";

        // 対象のコントローラを実行
        $controller = new $controllerName();
        $controller->$action();
    }
}
