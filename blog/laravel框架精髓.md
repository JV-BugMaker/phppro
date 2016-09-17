#LARAVEL框架

##自动加载
>laravel中对外自动加载文件就是autoload.php,此文件中有包含了composer中的autoload_real.php.real中定义了一个类.

##PHP对类自动加载处理之__autoload
>在PHP5中，当我们实例化一个未定义的类的时候就会触发 __autoload函数，在这边就可以处理未定义的类。

```
<?php
function autoLoad($class){
	$file = $class.'.class.php';
	if(is_file($file)){
		return require $file;
	}
}
spl_autoload_register('autoload);

```
上面就是函数方式进行处理，只需要将自动加载函数处理未定义的类。如何通过类静态加载处理。

```
<?php 
class Auto{
	public static function load($class){
		$file = $class.'.class.php';
		if(is_file($file)){
			return $file;
		}
	}
}
//使用数组方式进行处理，如果使用了框架的话，需要把namespace写入到数组第一个
spl_autoload_register(array('Auto','load'));

```

>call_user_func函数是系统内置函数，该函数允许用户调用直接写的函数并传入一定的参数。

```
<?php
function nowmagic($a,$b){
	echo $a.'|'.$b;
}
call_user_func('nowmagic',11111,2222);
```
##Laravel框架初识

###app目录介绍
Console:主要包含所有的artisan命令。

Events：用来放置于事件相关的类。

Exceptions：包含应用程序的异常处理类，用于处理应用程序抛出的任何异常。

Http：主要包含路由文件、控制器文件、请求文件、中间文件等，是应用程序与Laravel框架源代码等外部库交互的主要地方。

Jobs：主要包含消息队列的各种消息类文件。

Listeners：主要包含监听事件类文件。

Providers：主要包含服务提供者的相关文件。


###Vendor目录

composer：主要包含composer按照PSR规范生成的自动加载类。

laravel：包含Laravel框架源代码，代码部分都包含在vendor\laravel\framework\src\Illuminate文件夹下，该文件夹下每一个文件夹都是一个组件。

symfony：Laravel框架的底层（如请求类、响应类、文件管理类等）使用了symfony框架部分。

monolog：抱哈日志记录模块文件。

phpunit：包含程序单元测试模块文件。

###laravel框架路由

```
//在routes.php文件中 做路由处理
//match 表示匹配 参数一  http请求方式（多个采用数组的方式） 参数二 对应的url中path 参数三就是回调函数  进行对应处理
Route::match(['get','post'],'/',function(){
	return 'Hello';
});
Route::any('home',function(){});
```
路由参数：

```
Route::get('资源标识/{参数名[?][/{参数名}...]}',闭包函数或控制器响应函数标识)
[->where('参数名','正则表达式')];
Route::get('user/{name}',function($name){
	return $name;
})->where('name','[A-Za-z]+');

```
路由命名：

```
Route::get('user/name',['as'=>'name','user'=>function(){
	return "laravel";
}]);
/name => /user/name
```

路由群组：

```
Route::get('user/id',['middlewave'=>'auth',function(){
	return 'auth';
}]);
Route::get('user/name',['middlewave'=>'auth',function(){
	return 'auth name';
}]);
//上述 定义的两条路由信息 相同点就是都是前缀为user 中间件为auth  此时就可以定义一个群组

Route::group(['prefix'=>'user','midddlewave'=>'auth'],function(){
	Route::get('id',function(){
	});
	Route::get('name',function(){
	});
	//这边定义了 在group群组中详细的路由
});
```

基础控制器路由：

```
Route::controller('资源标识/{参数名[?][/{参数名}...]}','controller@action');

Route::get('home/{name}','HomeController@index');
```

隐式控制器路由

```
Route::controller('prefix','controller'[,命名路由]);
Route::controller('home','HomeController');
```

RESTFul资源控制器路由

>RESTful架构风格规定，数据的元操作，即CRUD(create, read, update和delete,即数据的增删查改)操作，分别对应于HTTP方法：GET用来获取资源，POST用来新建资源（也可以用于更新资源），PUT用来更新资源，DELETE用来删除资源，这样就统一了数据操作的接口，仅通过HTTP方法，就可以完成对数据的所有增删查改工作。

* GET（SELECT）：从服务器取出资源（一项或多项）。

* POST（CREATE）：在服务器新建一个资源。
* PUT（UPDATE）：在服务器更新资源（客户端提供完整资源数据）。
* PATCH（UPDATE）：在服务器更新资源（客户端提供需要修改的资源数据）。
* DELETE（DELETE）：从服务器删除资源。

```
Route::resource('根资源标识','控制器类名');
//一半用的比较多的就是隐式控制路由
```

在laravel中的模块引擎使用的是blade，跟node中的ejs很像。

##Laravel框架中的设计模式

###compact
>简历一个数组，包括变量名和变量值

```
$city  = "San Francisco";
$state = "CA";
$event = "SIGGRAPH";

$location_vars = array("city", "state");

$result = compact("event", "nothing_here", $location_vars);
print_r($result);
//输出值
Array
(
    [event] => SIGGRAPH
    [city] => San Francisco
    [state] => CA
)
```

###核心内容IoC
>在laravel中最重要，最核心的部分就是IoC，依赖外部注入的设计核心。如何理解这部分也是比较困难的。首先贴代码吧。

```
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
```

>下面贴自己的理解,简单的demo

```
<?php

//laravel  IOC容器 与依赖注入
class Container {
    private $container = [];
    public function bind($name, Closure $c) {
        $this->container[$name] = $c($this);
    }

    public function get($name, $params=[]) {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }
        return call_user_func($this->container[$name], $params);
        //return $this->container[$name]($this); //赋予当前container上下文
    }
}

class Config {
    public $env = 'config s  test';

    public function __construct(Container $container) {
    }

    public function getConfig() {
        return 'config...config';
    }

}

class Request {
    private $config;
    public function __construct($container) {
        $this->config = $container->get('config');
    }

    public function getConfig() {
       echo   $this->config->getConfig();
    }
}

$container = new Container;
$container->bind('config', function ($container) {
    return new Config($container);
});
//request module
$container->bind('request', function ($container) {
    return new Request($container);
});

$container->get('request')->getConfig();
```



