<?php

use \Hades\Dao\Config;
use \Hades\Dao\Register;

class DaoTest extends \Hades\Test\TestCase
{
    protected $container;

    public function setup()
    {
        $this->container = \Hades\Container\Container::instance();

        $dao = [
            'testdao' => [
                'pk' => 'test_id',
            ],
        ];

        Register::load($this->container, $dao);
    }

    public function testFacade()
    {
        $config = \TestdaoDao::config();
        $this->assertEquals('testdao', $config->table());
        $this->assertEquals('test_id', $config->pk());
    }
}
