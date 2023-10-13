<?php

namespace Mustafa\Staller;

class Cache
{
    private static $cache_dir = "../App/cache/";

    public static function get($file)
    {
        if (!file_exists(self::$cache_dir . $file)) {
            return false;
        }
        $data = file_get_contents(self::$cache_dir . $file);
        return unserialize($data);
    }

    public static function set($file, $data, $ttl = 0)
    {
        file_put_contents(self::$cache_dir . $file, serialize($data));
        if ($ttl > 0) {
            touch(self::$cache_dir . $file, time() + $ttl);
        }
    }
}
