<?php

use Hades\Facade\Facade;
use Hades\Container\Container;

class ContainerFacade {

    public function bar1()
    {
        return 'public bar';
    }

    private function bar2()
    {
        return 'private bar';
    }

    public static function bar3()
    {
        return 'public static bar';
    }

}


class ContainerTest extends \Hades\Test\TestCase
{
    private $container;

    public function setup()
    {
        $container = Container::instance();
        $container->load(['bind' => ['ContanerContract' => '\ContainerFacade']]);
    }

    public function testCall()
    {
        $foo = new \ContanerContract();

        $this->assertEquals('public bar', $foo->bar1());

        //$this->assertEquals('private bar', $foo->bar2());

        $this->assertEquals('public bar', \ContanerContract::bar1());

        $this->assertEquals('public static bar', \ContanerContract::bar3());

        // this will show warning
        @$bar1 = \ContanerContract::bar1();

        $this->assertEquals('public bar', $bar1);
    }
}
