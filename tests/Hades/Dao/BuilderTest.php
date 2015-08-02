<?php

use \Hades\Dao\Config;
use \Hades\Dao\Register;
use \Hades\Dao\Builder;
use \Hades\Dao\Connection;

class BuilderTest extends \Hades\Test\TestCase
{
    protected $container;

    public function setup()
    {
        $this->container = \Hades\Container\Container::instance();

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

    public function testPrepare()
    {
        $config = \TestDao::config();
        $builder = new Builder($config);
        $builder->where('id', 1);
        $builder->orWhere('field1', 2);
        $builder->whereIn('field2', [1,2]);
        $builder->orderBy('field3', 'desc');
        $builder->offset(1);
        $builder->limit(10);
        $builder->columns(['id']);

        list($sql, $value) = $builder->prepare();
        $expect = 'SELECT id FROM test WHERE  id =  ? or field1 =  ? and field2 in  (?,? ) ORDER BY  field3 desc OFFSET 1  LIMIT 10 ';
        $this->assertEquals($expect, $sql);
        $this->assertEquals(4, count($value));
    }

    public function testCURD()
    {
        $config = \TestDao::config();
        $builder = new Builder(\TestDao::config());

        // INSERT
        $builder->action('INSERT');
        $builder->set('field1' , 1)->set('field2', 1)->set('field3', 1);
        $builder->execute();
        $id = $builder->lastInsertId();

        // GET
        $builder = new Builder(\TestDao::config());
        $builder->where('id', $id);
        $testModel = $builder->get();
        $this->assertEquals($id, $testModel->id);

        // UPDATE
        $builder = new Builder(\TestDao::config());
        $builder->action('UPDATE');
        $builder->where('id', $id);
        $builder->set('field2', 2);
        $builder->execute();

        $builder = new Builder($config);
        $testModel = $builder->where('id', $id)->get();
        $this->assertEquals(2, $testModel->field2);

        // INSERT
        $builder = new Builder($config);
        $builder->action('INSERT');
        $builder->set('field1' , 2)->set('field2', 1)->set('field3', 1);
        $builder->execute();

        $builder = new Builder($config);
        $testModels = $builder->gets();
        $this->assertEquals(2, count($testModels));

        // DELETE
        $builder = new Builder($config);
        $builder->action('DELETE');
        $builder->where('id', $testModel->id);
        $builder->execute();

        $builder = new Builder($config);
        $testModels = $builder->gets();
        $this->assertEquals(1, count($testModels));
    }
}
