<?php

namespace Mustafa\Staller;

use App\handlers\onError;
use Error;
use Exception;

class Response
{
    public static $controller;
    public static $view;

    private static function controller($controller, $action)
    {
        if (!validator::hasController($controller)) {
            http_response_code(404);
            throw new Error("$controller not found", 101);
        }

        return (new $controller)->Call(action: $action);
    }

    public static function view(string $filename, array|object $_data = []): mixed
    {
        if (!validator::hasView($filename)) {
            http_response_code(404);
            throw new Exception("views($filename) not found");
            return false;
        }

        extract((array) $_data);
        unset($_data);
        ob_start();
        require(realpath("../App/views/$filename.php"));
        return ob_get_clean();
    }

    public static function Respond($route): mixed
    {
        if (!empty($route->controller)) {
            $controller = self::$controller = json_decode($route->controller);
            return self::controller($controller->class, $controller->action);
        }

        if (!empty($route->view)) {
            $view = self::$view = json_decode($route->view);
            return self::view($view->filename);
        }

        http_response_code(404);
        return onError::Response(message: "view or Controller not found", status: 404);
    }
}
