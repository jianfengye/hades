<?php

use \Hades\Dao\Config;
use \Hades\Dao\Register;
use \Hades\Dao\Connection;
use \Hades\Dao\Builder;

class CollectionTest extends \Hades\Test\TestCase
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
        $testModel->field1 = 101;
        $testModel->field2 = 101;
        $testModel->save();

        $testModel2 =  new \TestModel();
        $testModel2->field1 = 102;
        $testModel2->field2 = 102;
        $testModel2->save();

        $test2Model = new \Test2Model();
        $test2Model->field1 = 201;
        $test2Model->field2 = 201;
        $test2Model->save();

        $test2Model2 = new \Test2Model();
        $test2Model2->field1 = 202;
        $test2Model2->field2 = 202;
        $test2Model2->save();

        $testModels = \TestDao::gets();
        $this->assertEquals(2, count($testModels));

        $testModel->load('relation1');
        foreach ($testModels as $tmp) {
            $this->assertEmpty($tmp->relation1);
        }

        $testModel->field1 = $test2Model->id;
        $testModel->save();
        $testModel2->field1 = $test2Model2->id;
        $testModel2->save();

        $testModels = \TestDao::gets();
        $testModels->load('relation1');

        foreach ($testModels as $tmp) {
            $this->assertGreaterThan(0, $tmp->relation1->id);
        }

        $testModels->load('relation2');
        foreach ($testModels as $tmp) {
            $this->assertEmpty($tmp->relation2);
        }

        $testModel->load('relation3');
        foreach ($testModels as $tmp) {
            $this->assertEmpty($tmp->relation3);
        }

        $test2Model->field2 = $testModel->id;
        $test2Model->save();
        $test2Model2->field2 = $testModel2->id;
        $test2Model2->save();

        $testModels->load('relation2');
        foreach ($testModels as $tmp) {
            $this->assertEquals(1, count($tmp->relation2));
        }

        $testModels->load('relation3');
        foreach ($testModels as $tmp) {
            $this->assertGreaterThan(0, $tmp->relation3->id);
        }
    }
}
