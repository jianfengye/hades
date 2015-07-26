<?php

class Foo
{
    public function sayHello()
    {
        echo 'hello';
    }
}


class_alias('Foo', 'Bar');
class_alias('Foo', 'Bar2');


$bar2 = new Bar2();
$bar2->sayHello();
