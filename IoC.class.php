<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/11
 * Time: 下午4:45
 * IoC 模式又称为依赖注入模式 。 控制反转是将组件间的依赖关系从程序内部提到外部容器来管理
 * ,而依赖注入是指组件的依赖通过外部以参数或其他形式注入,两种说法本质是一个意思
 */

class Traveller{
    protected $trafficTool;

    public function __construct(Visit $trafficTool)
    {
        $this->trafficTool = $trafficTool;
    }

    public function visitTibet()
    {
        $this->trafficTool->go();
    }
}
//在此将原本在内部实现的交通方式 挪到外部去实现  较少耦合  作为参数传入  当需要修改的时候 不需要去内部实现
//生成依赖的交通工具实例
$trafficTool = new Leg();

$tra = new Traveller($trafficTool);

$tra->visitTibet();
