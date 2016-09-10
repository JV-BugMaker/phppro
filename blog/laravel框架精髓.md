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