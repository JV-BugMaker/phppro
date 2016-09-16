<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/16
 * Time: 上午11:55
 *
 * laravel  设计服务器容器方式
 */

//设计容器类,容器类装实例或提供实例的回调函数
class Container
{
    //用于装提供实例的回调函数,真正的容器还会装实例等其他内容

    //从而实现单例模式

    protected $bindings = [];

    public function bind($abstract,$concrete = null,$shared = false){

        //判断是否是匿名函数类的实例
        if(! $concrete instanceof Closure){
            //如果提供的参数不是回调函数,则产生默认的回调函数
            $concrete = $this->getClosure($abstract,$concrete);
        }
        //['concrete']=>$concrete ['shared'] =>  $shared
        $this->bindings[$abstract] = compact('concrete','shared');
    }

    //默认生成实例的回调函数
    protected function getClosure($abstract,$concrete){
        //生成实例的回调函数  $c一般为IoC容器对象,在调用生成实例式提供
        //即build函数中的$concrete($this)
        return function($c) use($abstract,$concrete){
            $method = ($abstract== $concrete) ?'build':'make';
            return $c->$method($concrete);
        };
    }

    //生成实例对象,首先解决接口和要实例化类之间的依赖关系
    //创建实例对象

    public function make($abstract){
        $concrete = $this->getConcrete($abstract);

        if($this->isBuildable($concrete,$abstract)){
            $object = $this->build($concrete);
        }else {
            $object = $this->make($concrete);
        }

        return $object;
    }

    protected function isBuildable($concrete,$abstract){
        return $concrete === $abstract||$concrete instanceof Closure;
    }

    //获取绑定的回调函数

    protected  function getConcrete($abstract){
        if(! isset($this->bindings[$abstract])){
            return $abstract;
        }
        return $this->bindings[$abstract]['concrete'];
    }
    //实例化对象

    public function build($concrete){
        //此函数就是用来具体实例化对象
        if($concrete instanceof Closure){
            //传入的是object
            return $concrete($this);
        }
        //映射
        $reflector = new ReflectionClass($concrete);
        if(! $reflector->isInstantiable()){
            echo $message = "Target[$concrete] is not Instantiable";
        }

        $constructor = $reflector->getConstructor();

        if(is_null($constructor)){
            return new $concrete;
        }
        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    //解决通过反射机制实例化对象时的依赖

    protected function getDependencies($parameters)
    {
        $dependencies = [];
        foreach($parameters as $parameter){
            $dependency = $parameter->getClass();
            if(is_null($dependency)){
                $dependencies[] = NULL;
            }else{
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array)$dependencies;
    }

    protected function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }
}


class Traveller
{
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




interface Visit {
    public function go();
}


class Train implements  Visit
{
    public function go(){
        echo "train";
    }
}

//实例化IoC容器
$app = new Container();

$app->bind("Visit","Train");

$app->bind("traveller","Traveller");
//通过容器实现依赖注入 完成类的实例化

$tra = $app->make("traveller");
$tra->visitTibet();