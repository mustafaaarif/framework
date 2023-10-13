<?php

namespace Mustafa\Staller;

class RouteFinder extends Route
{
    private static $CurrentUrlKey;

    public static function hasRoute($url): bool
    {
       
        $ReqRouteArr = self::urlArr(url: $url);

        foreach (self::$routes as $routeId => $routeData) {

            if ($_SERVER['REQUEST_METHOD'] !== $routeData['method']) continue;

            $routeUrl = $routeData['url'];

            $RouteArr = self::urlArr(url: $routeUrl);

            if (($ReqRouteArr !== '/' && $RouteArr !== '/') && count($ReqRouteArr) !== count($RouteArr)) continue;

            $route = self::urlBind(url: $RouteArr);
            $routeReq = self::urlBind(url: $ReqRouteArr);

            if ($route !== $routeReq) continue;
            self::$CurrentUrlKey = $routeId;
            return true;
        }

        return false;
    }

    public static function getMatchUrlData(): object
    {
        return (object) self::$routes[self::$CurrentUrlKey];
    }

    protected static function urlArr(string $url): array|string
    {
        if ($url != '/') $url = explode("/", trim($url, "/"));
        return $url;
    }

    protected static function urlBind(array|string $url): string
    {
        if ($url != '/') $url = md5(implode("-", $url));
        return $url;
    }
}