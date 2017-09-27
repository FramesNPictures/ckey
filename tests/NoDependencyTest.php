<?php

use Fnp\CKey\CKey;

class NoDependencyTest extends PHPUnit_Framework_TestCase
{
    public function testSomething()
    {
        $d1 = new ModelA();
        $d1->id = 1;
        $d2 = new ModelA();
        $d2->id = 2;
        $d3 = new ModelA();
        $d3->id = 2;
        $d3->test = 'dupa';

        $a = new A($d1);
        $b = new A($d2);
        $c = new A($d3);
        $d = new B($d3);

        dd($a(), $b(), $c(), $d());

        $a = new A();
        $b1 = new B($a, 1);
        $b2 = new B($a, 2);

        var_dump($a(), $b1(), $b2());

        A::forget();

        var_dump($a(), $b1(), $b2());
    }
}

class ModelA extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'test';
}

class A extends CKey
{

}

class B extends CKey
{

}