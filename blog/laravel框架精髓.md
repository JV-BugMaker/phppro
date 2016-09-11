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

```
Route::resource('根资源标识','控制器类名');
//一半用的比较多的就是隐式控制路由
```

在laravel中的模块引擎使用的是blade，跟node中的ejs很像。


