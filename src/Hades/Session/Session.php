<?php

namespace Hades\Session;

use Hades\Config\Config;
use Hades\Session\Handler\FileHandler;

class Session
{
    private $handler;

    public function __construct()
    {
        if ($this->handler) {
            return;
        }

        // set handler by config
        $driver = 'file';
        if (Config::get('session.driver')) {
            $driver = Config::get('session.driver');
        }

        $lifetime = 60;
        if (Config::has('session.lifetime')) {
            $lifetime = Config::get('session.lifetime');
        }

        $this->handler = new FileHandler();
        switch($driver) {
            case "file" :
                session_save_path(Config::get('session.files'));
                $this->handler = new FileHandler();
                break;
        }

        session_set_save_handler($this->handler);
        session_set_cookie_params($lifetime);
        session_start();
    }

    // check is actived
    public function actived()
    {
        return boolval(session_status() == PHP_SESSION_ACTIVE);
    }

    public function get($key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function all()
    {
        return $_SESSION;
    }

    public function del($key)
    {
        unset($_SESSION[$key]);
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }
}
