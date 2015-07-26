<?php

use \Hades\Dao\Register;

class RegisterTest extends \Hades\Test\TestCase
{
    public function testLoad()
    {
        $container = \Hades\Container\Container::instance();
        $dao = [
            'testtable' => [
                'pk' => 'id',
            ],
            'test2_table' => [
                'pk' => 'id',
            ],
        ];

        Register::load($container, $dao);
        $this->assertEquals(true, $container->have('TesttableModel'));
        $this->assertEquals(true, $container->have('TesttableDao'));

        $this->assertEquals(true, $container->have('Test2TableModel'));
        $this->assertEquals(true, $container->have('Test2TableDao'));
    }
}
