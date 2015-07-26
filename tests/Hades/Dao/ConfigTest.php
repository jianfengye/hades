<?php

use \Hades\Dao\Config;

class ConfigTest extends \Hades\Test\TestCase
{
    public function testConfig()
    {
        $dao = [
            'test1' => [],
            'test2' => [
                'pk' => 'test_id',
                'model' => '\Hades\Models\Test2Model',
            ],
        ];

        foreach ($dao as $table => $config) {
            $$table = new Config($table, $config);
        }
        
        $this->assertEquals('test1', $test1->table());
        $this->assertEquals('id', $test1->pk());
        $this->assertEquals('\Hades\Dao\Model', $test1->model());

        $this->assertEquals('test_id', $test2->pk());
        $this->assertEquals('\Hades\Models\Test2Model', $test2->model());
    }
}
