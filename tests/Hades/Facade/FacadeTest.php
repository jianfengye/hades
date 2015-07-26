<?php

use Hades\Facade\Facade;

class FacadeFoo {
    use Facade;

    public static function alias() { return 'FacadeFoo'; }

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

class FacadeTest extends \Hades\Test\TestCase
{
    public function setup()
    {
        $container = \Hades\Container\Container::instance();
        $container->load(['bind' => ['FacadeFoo' => '\FacadeFoo']]);
    }

    public function testCall()
    {
        $foo = new FacadeFoo();

        $this->assertEquals('public bar', $foo->bar1());

        $this->assertEquals('private bar', \FacadeFoo::bar2());

        $this->assertEquals('public static bar', \FacadeFoo::bar3());

        // this will show warning
        @$bar1 = \FacadeFoo::bar1();

        $this->assertEquals('public bar', $bar1);
    }
}
