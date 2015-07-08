<?php

namespace Hades\Session;

use Hades\Config\Config;
use Hades\Facade\Facade;
use Hades\Session\Handler\FileHandler;

class Session
{
    use Facade;

    public static function getAlias()
    {
        return 'Session';
    }

    private $handler;

    public function __construct()
    {
        if ($this->handler) {
            return;
        }

        // set handler by config
        $driver = Config::get('session.driver');
        if (empty($driver)) {
            parent::__construct();
            return;
        }

        $this->handler = new FileHandler();
        switch($driver) {
            case "file" :
                session_save_path(Config::get('session.files'));
                $this->handler = new FileHandler();
                break;
        }

        session_set_save_handler($this->handler);
        session_start();
    }

    // check is actived
    protected function actived()
    {
        return boolval(session_status() == PHP_SESSION_ACTIVE);
    }

    protected function get($key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    protected function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    protected function all()
    {
        return $_SESSION;
    }

    protected function del($key)
    {
        unset($_SESSION[$key]);
    }

    protected function has($key)
    {
        return isset($_SESSION[$key]);
    }
}
