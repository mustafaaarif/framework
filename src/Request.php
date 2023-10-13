<?php

namespace Mustafa\Staller;

use staller\storage;
use App\handlers\onError;
use Exception;

class Request
{
    public static $inputs;
    public static $params = [];
    private static $error = [];

    private static function runMiddlewares($route): bool
    {
        $middlewares = json_decode($route->middleware);
        foreach ($middlewares->filenames as $filename) {

            if (!validator::hasMiddleware($filename)) {
                http_response_code(404);
                throw new Exception("middleware('$filename') not found in {$route->trace['file']} at line {$route->trace['line']}", 101);
            }

            $middleware = (new ("App\\middleware\\$filename"));

            if (!$middleware->onRun()) {
                self::$error['middleware'] = $middleware;
                return false;
            }
        }

        return true;
    }


    public static function validateAndRespond()
    {
        $uri = Request::getRequestUri();

        if (!RouteFinder::hasRoute($uri)) {
            return onError::handleUnmatchedRoute($uri);
        }

        $route = RouteFinder::getMatchUrlData();

        if (!self::runMiddlewares($route)) {
            return (self::$error['middleware'])->onError();
        }

        return Response::Respond($route);
    }

    public static function getBaseUrl(): string
    {
        $baseUrl = rtrim($_SERVER['HTTP_HOST'] . '/' . storage::getEnv("APP_PATH"), '/');
        return self::getProtocol() . rtrim($baseUrl, '/');
    }

    public static function getPublicUrl(): string
    {
        $publicDir = '/public'; // Adjust this to your public directory path
        $baseUrl = rtrim($_SERVER['HTTP_HOST'] . '/' . storage::getEnv("APP_PATH"), '/');
        $publicUrl = $baseUrl . $publicDir;

        return self::getProtocol() . rtrim($publicUrl, '/');
    }

    private static function sanitizeUrl($uri)
    {
        $uri = trim($uri);

        if (substr($uri, 0, 1) === '/' && $uri !== '/') {
            $uri = substr($uri, 1);
        }

        return $uri;
    }

    public static function getRequestUri(): string
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF']);
        $requestUri = ($requestUri['path'] ?? '/');

        $appPath = self::sanitizeUrl(storage::getEnv('APP_PATH'));
        $pattern = "/^\/?" . str_replace('/', '\/', $appPath) . "\//";

        $requestUri = self::sanitizeUrl(preg_replace($pattern, '/', $requestUri));

        return validator::sanitizeInput(filter_var(value: $requestUri, filter: FILTER_SANITIZE_URL));
    }

    public static function isApi(): bool
    {
        $apiPath = '/api/';
        return strpos(parse_url(self::getRequestUri(), PHP_URL_PATH), $apiPath) !== false;
    }

    public static function getServer(): string
    {
        return $_SERVER['SERVER_NAME'];
    }

    public static function getProtocol(): string
    {
        return isset($_SERVER['HTTPS']) ? "https://" : "http://";
    }

    public static function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public static function getClientIP(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getRequestHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('_', ' ', substr($key, 5));
                $headerName = ucwords(strtolower($headerName));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public static function hasQueryParam(string $paramName): bool
    {
        return isset($_GET[$paramName]);
    }

    public static function getQueryParam(string $paramName, $defaultValue = null)
    {
        return $_GET[$paramName] ?? $defaultValue;
    }

    public static function hasPostParam(string $paramName): bool
    {
        return isset($_POST[$paramName]);
    }

    public static function getPostParam(string $paramName, $defaultValue = null)
    {
        return $_POST[$paramName] ?? $defaultValue;
    }

    public static function getInputs(): array
    {
        return self::$inputs;
    }
}
