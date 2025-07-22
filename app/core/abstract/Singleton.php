<?php
namespace App\Core\Abstract;

abstract class Singleton
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    // Uncomment these if you want to prevent cloning and unserialization
    // private function __clone() {}
    // private function __wakeup() {}
}