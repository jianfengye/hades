<?php

use Hades\Session\Session;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    private $session;

    public function setup()
    {
        class_alias('\Hades\Config\Config', 'Config');
        $class = $this->getMockClass(
            'Config',
            array('get')
        );
        $class::staticExpects($this->any())->method('get')->will($this->returnValue('file'));

        $this->session = new \Hades\Session\Session();
    }

    public function testFlow()
    {
        $active = $this->session->actived();
        $this->assertEquals(true, $active);

        $this->session->set('test', 'testvalue');
        $value = $this->session->get('test');
        $this->assertEquals('testvalue', $value);

        $all = $this->session->all();
        $this->assertTrue(isset($all['test']));

        $this->assertTrue($this->session->has('test'));
    }


}
