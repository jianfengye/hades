## Hades PHP Framework

Hades是一款容易上手的PHP框架，它在很大程度上借鉴了laravel框架，但是对于laravel中很多部分进行重新思考和构思。

所以如果你使用过laravel框架，你会非常容易上手这个框架。

## 特点

* 简单，容易上手
* 简单配置即可以生成ORM
* 符合PSR的编程规范
* composer只加载最精简的第三方插件

## 路由

支持get,post,any,group

示例:


    <?php

    use Hades\Route\Manager as Route;

    Route::get('/login/index', '\App\Controllers\LoginController@index');
    Route::post('/login/login', '\App\Controllers\LoginController@login');
    Route::get('/login/logout', '\App\Controllers\LoginController@logout');

    Route::group(['middleware' => ['\App\Middlewares\AuthMiddleware']], function(){

        Route::get('/welcome/index', '\App\Controllers\WelComeController@index');

    });

## ORM

遵循配置即一切的原则，只需要配置好配置文件就可以直接使用

    <?php

    // put all table and relation to this config
    return [
        // 管理员表
        'admin' => [ 'pk' => 'id'],
        // 项目表
        'project' => [
            'pk' => 'id',
            'relations' => [
                'project_mechanics' => [
                    'type' => 'has_many',
                    'table' => 'project_mechanic',
                    'key' => 'id',
                    'relate_key' => 'project_id',
                ],
                'logs' => [
                    'type' => 'has_many',
                    'table' => 'log',
                    'key' => 'id',
                    'relate_key' => 'project_id',
                    'builder' => [
                        'orderBy' => ['created_at', 'desc'],
                        'offset' => [0],
                        'limit' => [20],
                    ],
                ]
            ]
        ],
        // 日志表
        'log' => [ 'pk' => 'id' ],

        // 服务器表
        'mechanic' => ['pk' => 'id'],
    ];

提供Dao和Model使用：

增加数据：

    $log = new \LogModel();
    $log->project_id = $project_id;
    $log->admin_id = $myself->id;
    $log->version = $reversion->sha1;
    $log->created_at = time();
    $log->save();

查找数据：

    $log = \LogDao::find(1);

更新数据：

    $log->version = $new;
    $log->save();

删除数据：

    $log->delete();

批量获取：

    $logs = \LogDao::finds([1,2]);

## Session

    \Session::set('admin', serialize($admin));
    \Session::del('admin');

## 目录结构

参考[hades_install](https://github.com/jianfengye/hades_install)

### License

The Hades framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
