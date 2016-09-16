<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/3
 * Time: 上午11:57
 * reflection 反射机制
 */
//PHP5 具有完整的反射API,添加了对类、接口、函数、方法和扩展进行反向工作的能力
class A{

    public static function call()
    {
        echo "Class A";
    }
    public static function newCall(){
        self::call();
        static::call();
    }
    public static function create(){
        //ge根据调用类 创建不同的实例
        $self = new self();
        $static = new static();
        return array($self,$static);
    }
}

class B extends A{
    public static function call(){
        echo "Class B";
    }
}
//调用反射 执行对类的反射
$ref = new ReflectionClass('A');
$instance = $ref->newInstanceArgs();
$instance->call();
B::call();
var_dump(B::create());

//trait 重要特性
//(1)优先级:当前类的方法会覆盖train中的方法,但是train中的方法又会覆盖基类中的方法。
//(2)多个train组合:通过逗号,通过use关键字列出多个trait
//(3)冲突的解决:如果两个trait都插入了一个相同的类中的命名冲突,需要使用一个 insteadof操作符来明确使用冲突方法中具体哪个trait。
//同时可以通过使用as操作符将其中一个的方法以另外的名称来引入。
//(4)修改方法的访问控制:使用as 语法可以调整方法的访问控制。
//(5)trait的抽象方法:在trait方法中可以使用抽象成员,使得类中必须实现这个抽象方法。
//(6)trait的静态成员:在trait中可以调用静态方法和静态变量。
//(7)trait的属性定义:在trait中同样可以定义属性。

//简化三元运算
$value = 1?:-1;
echo $value;