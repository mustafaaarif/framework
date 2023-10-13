<?php

namespace Mustafa\Staller;

use Exception;

class Route
{
    public static array|object $routes = [];
    public static array|object $group = [];
    private static $instance;

    public static function getInstance()
    {
        return self::$instance ??= new self;
    }

    static function __callStatic($method, $arguments)
    {
        $allowMethod = ['get', 'post', 'delete', 'put'];

        ['file' => $file, 'line' => $line] = debug_backtrace()[0];
        $file = basename($file);

        if (!in_array($method, $allowMethod)) {
            throw new Exception("Method Not Found In $file at line $line", 101);
        }

        extract($arguments);
        $route = ['url' => $url, 'method' => strtoupper($method)] + ['group' => self::$group, 'trace' => compact('file', 'line')];
        self::$routes[$name ?? count(self::$routes)] = $route;
        return self::getInstance();
    }

    public static function controller(string $class, string $action): object
    {
        self::$routes[array_key_last(self::$routes)]['controller'] = json_encode(compact('class', 'action'));
        return self::getInstance();
    }

    public static function middleware(array $filenames, $callBack = null): false|self
    {
        if (is_callable($callBack)) {
            self::$group['middleware'] = $filenames;
            $callBack();
            self::$group['middleware'] = [];
            return false;
        }

        $filenames = array_merge($filenames, self::$group['middleware'] ?? []);
        self::$routes[array_key_last(self::$routes)]['middleware'] = json_encode(['filenames' => $filenames]);
        return self::getInstance();
    }

    public static function view(string $filename): object
    {
        self::$routes[array_key_last(self::$routes)]['view'] = json_encode(compact('filename'));
        return self::getInstance();
    }
}
