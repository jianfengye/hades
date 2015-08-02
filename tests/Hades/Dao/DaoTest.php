<?php

use \Hades\Dao\Config;
use \Hades\Dao\Register;
use \Hades\Dao\Connection;
use \Hades\Dao\Builder;

class DaoTest extends \Hades\Test\TestCase
{
    protected $container;
    protected $connection;

    public function setup()
    {
        $this->container = \Hades\Container\Container::instance();

        $dao = [
            'testdao' => [
                'pk' => 'test_id',
            ],
            'test2' => [],
        ];

        Register::load($this->container, $dao);

        $dao = [
            'test' => [
                'pk' => 'id',
            ],
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
            create table test (
                id int auto_increment primary key,
                field1 int,
                field2 int,
                field3 int,
                field4 int
            )
        ');
    }

    public function testFacade()
    {
        $config = \TestdaoDao::config();
        $this->assertEquals('testdao', $config->table());
        $this->assertEquals('test_id', $config->pk());

        $config2 = \Test2Dao::config();
        $config = \TestdaoDao::config();
        $this->assertEquals('testdao', $config->table());
        $this->assertEquals('test_id', $config->pk());
    }

    public function testDao()
    {
        // INSERT
        $builder = new Builder(\TestDao::config());
        $builder->action('INSERT');
        $builder->set('field1' , 1)->set('field2', 1)->set('field3', 1);
        $builder->execute();
        $id = $builder->lastInsertId();

        $builder = new Builder(\TestDao::config());
        $builder->action('INSERT');
        $builder->set('field1' , 2)->set('field2', 1)->set('field3', 1);
        $builder->execute();
        $id2 = $builder->lastInsertId();

        $testModel = \TestDao::find($id, ['id', 'field1']);
        $this->assertEquals($id, $testModel->id);
        $this->assertEquals(1, $testModel->field1);
        $this->assertNull($testModel->field2);

        $testModels = \TestDao::finds([$id, $id2]);
        $this->assertEquals(2, count($testModels));

        $testModel = null;
        $testModel = \TestDao::get(['field1' => 1], ['field2' => 'desc']);
        $this->assertEquals(1, $testModel->field1);

        $testModels = [];
        $testModels = \TestDao::gets([ 'field2' => 1]);
        $this->assertEquals(2, count($testModels));

        $count = \TestDao::num([ 'field2' => 1]);
        $this->assertEquals(2, $count);

        \TestDao::delete([ 'field1' => ['>' , '0']]);

        $testModels = \TestDao::gets(['field1' =>  1]);
        $this->assertEquals(0, count($testModels));
    }
}
