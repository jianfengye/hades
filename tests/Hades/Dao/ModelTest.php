<?php

use \Hades\Dao\Config;
use \Hades\Dao\Register;
use \Hades\Dao\Connection;
use \Hades\Dao\Builder;

class ModelTest extends \Hades\Test\TestCase
{
    protected $container;
    protected $connection;

    public function setup()
    {
        $this->container = \Hades\Container\Container::instance();

        $dao = [
            'test' => [
                'pk' => 'id',
            ],
            'test2' => [
                'pk' => 'id',
            ]
        ];

        Register::load($this->container, $dao);

        // create table testdao
        $this->connection = new Connection('master', [
            "hostname" => '192.168.33.10',
            "driver" => 'mysql',
            "port" => '3306',
            'password' => 'yejianfeng',
            'username' => 'yejianfeng',
            'database' => 'hades',
        ]);

        $this->connection->pdo()->exec('
            drop table if exists test
        ');
        $this->connection->pdo()->exec('
            drop table if exists test2
        ');

        $this->connection->pdo()->exec('
            create table test (
                id int auto_increment primary key,
                field1 int,
                field2 int,
                field3 int,
                field4 int
            )
        ');

        $this->connection->pdo()->exec('
            create table test2 (
                id int auto_increment primary key,
                field1 int,
                field2 int,
                field3 int,
                field4 int
            )
        ');
    }

    public function testModel()
    {
        $testModel = new \TestModel();
        $testModel->field1 = 1;
        $testModel->field2 = 2;
        $testModel->save();
        
        $this->assertEquals(1, $testModel->id);

        $testModel->field2 = 3;
        $testModel->save();

        $testModel = \TestDao::find($testModel->id);
        $this->assertEquals(3, $testModel->field2);

        $testModel->delete();

        $num = \TestDao::num(['field1' => 1]);
        $this->assertEquals(0, $num);
    }

    public function testRelation()
    {
        $dao = [
            'test' => [
                'pk' => 'id',
                'relations' => [
                    'relation1' => [
                        'type' => 'has_one',
                        'table' => 'test2',
                        'key' => 'field1',
                        'relate_key' => 'id',
                    ],
                    'relation2' => [
                        'type' => 'has_many',
                        'table' => 'test2',
                        'key' => 'id',
                        'relate_key' => 'field2',
                    ],
                    'relation3' => [
                        'type' => 'belong_to',
                        'table' => 'test2',
                        'key' => 'id',
                        'relate_key' => 'field2',
                    ]
                ],
            ],
            'test2' => [
                'pk' => 'id',
            ]
        ];

        Register::load($this->container, $dao);

        $testModel =  new \TestModel();
        $testModel->field1 = 100;
        $testModel->field2 = 100;
        $testModel->save();

        $test2Model = new \Test2Model();
        $test2Model->field1 = 200;
        $test2Model->field2 = 200;
        $test2Model->save();

        $testModel->load('relation1');
        $this->assertEmpty($testModel->relation1);

        $testModel->field1 = $test2Model->id;
        $testModel->save();
        $testModel->load('relation1');
        $this->assertEquals($test2Model->id, $testModel->relation1->id);


        $testModel->load('relation2');
        $this->assertEmpty($testModel->relation2);

        $testModel->load('relation3');
        $this->assertEmpty($testModel->relation3);

        $test2Model->field2 = $testModel->id;
        $test2Model->save();
        $testModel->load('relation2');
        $this->assertEquals(1, count($testModel->relation2));

        $testModel->load('relation3');
        $this->assertEquals($test2Model->id, $testModel->relation3->id);
    }
}
