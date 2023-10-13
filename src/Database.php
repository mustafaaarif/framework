<?php

namespace Mustafa\Staller;

use Illuminate\Database\Capsule\Manager as Eloquent;
use PDO, PDOException;

class Database
{
    public static function Eloquent()
    {
        $Eloquent = new Eloquent;

        $Eloquent->addConnection([
            'driver'    => 'mysql',
            'host'      => storage::getEnv('DB_SERVER'),
            'database'  => storage::getEnv('DB_DATABASE'),
            'username'  => storage::getEnv('DB_USER'),
            'password'  => storage::getEnv('DB_PASS')
        ]);

        // Make this Capsule instance available globally via static methods... (optional)
        $Eloquent->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $Eloquent->bootEloquent();
    }

    public function manually()
    {
        $dsn = "mysql:host=localhost;dbname=user";
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=localhost;dbname=user", 'root', '');
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        } finally {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        }
    }
}
