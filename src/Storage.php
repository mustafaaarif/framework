<?php

namespace Mustafa\Staller;

class storage
{
    public static $storage = ['env' => null];

    public static function setEnv()
    {
        self::$storage['env'] = parse_ini_file('../App/.env');
    }

    public static function getEnv($name = null)
    {
        if (self::$storage['env'] === null) {
            self::setEnv();
        }
        return $name === null ? self::$storage['env'] : self::$storage['env'][$name];
    }

    public static function get($name = null)
    {
        return $name === null ? self::$storage : self::$storage[$name] ?? null;
    }

    public static function set($name, $data): void
    {
        self::$storage[$name] = $data;
    }
}