ctype_alnum  -- 做字母和数字字符检测
basename — 返回路径中的文件名部分
(PHP 4 >= 4.2.0, PHP 5, PHP 7)
pg_escape_string — 转义 text/char 类型的字符串-----替换 addslashes()。
-- 是 SQL 的注释标记，一般可以使用来它告诉 SQL 解释器忽略后面的语句。
(PHP 4, PHP 5, PHP 7)
settype — 设置变量的类型
    type 的可能值为： “boolean” （或为“bool”，从 PHP 4.2.0 起） “integer” （或为“int”，从 PHP 4.2.0 起） “float” （只在 PHP 4.2.0 之后可以使用，对于旧版本中使用          的“double”现已停用） "string"  "array"  "object" “null” （从 PHP 4.2.0 起）

几个 PHP 的“魔术常量”
名称	说明
__LINE__	文件中的当前行号。
__FILE__	文件的完整路径和文件名。如果用在被包含文件中，则返回被包含的文件名。自 PHP 4.0.2 起，__FILE__ 总是包含一个绝对路径（如果是符号连接，则是解析后的绝对路径），而在此之前的版本有时会包含一个相对路径。
__DIR__	文件所在的目录。如果用在被包括文件中，则返回被包括的文件所在的目录。它等价于 dirname(__FILE__)。除非是根目录，否则目录中名不包括末尾的斜杠。（PHP 5.3.0中新增） =
__FUNCTION__	函数名称（PHP 4.3.0 新加）。自 PHP 5 起本常量返回该函数被定义时的名字（区分大小写）。在 PHP 4 中该值总是小写字母的。
__CLASS__	类的名称（PHP 4.3.0 新加）。自 PHP 5 起本常量返回该类被定义时的名字（区分大小写）。在 PHP 4 中该值总是小写字母的。类名包括其被声明的作用区域（例如 Foo\Bar）。注意自 PHP 5.4 起 __CLASS__ 对 trait 也起作用。当用在 trait 方法中时，__CLASS__ 是调用 trait 方法的类的名字。
__TRAIT__	Trait 的名字（PHP 5.4.0 新加）。自 PHP 5.4 起此常量返回 trait 被定义时的名字（区分大小写）。Trait 名包括其被声明的作用区域（例如 Foo\Bar）。
__METHOD__	类的方法名（PHP 5.0.0 新加）。返回该方法被定义时的名字（区分大小写）。
__NAMESPACE__	当前命名空间的名称（区分大小写）。此常量是在编译时定义的（PHP 5.3.0 新增）。


从函数返回一个引用，必须在函数声明和指派返回值给一个变量时都使用引用运算符 &：
<?php
function &returns_reference()
{
    return $someref;
}

$newref =& returns_reference();

<?php
function &bar()
{
    $a = 5;
    return $a;
}
foo(bar());
?>


当 unset 一个引用，只是断开了变量名和变量内容之间的绑定。这并不意味着变量内容被销毁了

运算符优先级
结合方向	运算符	附加信息
无	clone new	clone 和 new
左	[	array()
右	**	算术运算符
右	++ -- ~ (int) (float) (string) (array) (object) (bool) @	类型和递增／递减
无	instanceof	类型
右	!	逻辑运算符
左	* / %	算术运算符
左	+ - .	算术运算符和字符串运算符
左	<< >>	位运算符
无	< <= > >=	比较运算符
无	== != === !== <> <=>	比较运算符
左	&	位运算符和引用
左	^	位运算符
左	|	位运算符
左	&&	逻辑运算符
左	||	逻辑运算符
左	??	比较运算符
左	? :	ternary
right	= += -= *= **= /= .= %= &= |= ^= <<= >>=	赋值运算符
左	and	逻辑运算符
左	xor	逻辑运算符
左	or	逻辑运算符

对象复制可以通过 clone 关键字来完成（如果可能，这将调用对象的 __clone() 方法）。

$a <=> $b	结合比较运算符	当$a小于、等于、大于than $b时 分别返回一个小于、等于、大于0的integer 值。 PHP7开始提供.
$a ?? $b ?? $c	NULL 合并操作符	从左往右第一个存在且不为 NULL 的操作数。如果都没有定义且不为 NULL，则返回 NULL。PHP7开始提供。
list — 把数组中的值赋给一些变量。
PHP 支持一个执行运算符：反引号（``）。注意这不是单引号！PHP 将尝试将反引号中的内容作为外壳命令来执行，并将其输出信息返回（即，可以赋给一个变量而不是简单地丢弃到标准输出）。使用反引号运算符“`”的效果与函数 shell_exec() 相同。
echo "<pre>";
$output = `ls -al`;                 
echo $output;die;

数组运算符
例子	名称	结果
$a + $b	联合	$a 和 $b 的联合。
$a == $b	相等	如果 $a 和 $b 具有相同的键／值对则为 TRUE。
$a === $b	全等	如果 $a 和 $b 具有相同的键／值对并且顺序和类型都相同则为 TRUE。
$a != $b	不等	如果 $a 不等于 $b 则为 TRUE。
$a <> $b	不等	如果 $a 不等于 $b 则为 TRUE。
$a !== $b	不全等	如果 $a 不全等于 $b 则为 TRUE。


declare 结构用来设定一段代码的执行指令。declare 的语法和其它流程控制结构相似：
declare (directive)
    statement
directive 部分允许设定 declare 代码段的行为。目前只认识两个指令：ticks（更多信息见下面 ticks 指令）以及 encoding（更多信息见下面 encoding 指令）。
Note: encoding 是 PHP 5.3.0 新增指令。
declare(ticks=1) {
    // entire script here
}
Tick（时钟周期）是一个在 declare 代码段中解释器每执行 N 条可计时的低级语句（zend engine）就会发生的事件。N 的值是在 declare 中的 directive 部分用 ticks=N 来指定的。
在每个 tick 中出现的事件是由 register_tick_function() 来指定的。更多细节见下面的例子。注意每个 tick 中可以出现多个事件。

闭包可以从父作用域中继承变量。 任何此类变量都应该用 use 语言结构传递进去。
// 继承 $message
$example = function () use ($message) {
    var_dump($message);
};
echo $example();

Heredoc 结构
第三种表达字符串的方法是用 heredoc 句法结构：<<<。在该运算符之后要提供一个标识符，然后换行。接下来是字符串 string 本身，最后要用前面定义的标识符作为结束标志。
结束时所引用的标识符必须在该行的第一列，而且，标识符的命名也要像其它标签一样遵守 PHP 的规则：只能包含字母、数字和下划线，并且必须以字母和下划线作为开头。
public $bar = <<<EOT
bar
    EOT;

Nowdoc 结构
就象 heredoc 结构类似于双引号字符串，Nowdoc 结构是类似于单引号字符串的。Nowdoc 结构很象 heredoc 结构，但是 nowdoc 中不进行解析操作。这种结构很适合用于嵌入 PHP 代码或其它大段文本而无需对其中的特殊字符进行转义。与 SGML 的 <![CDATA[ ]]> 结构是用来声明大段的不用解析的文本类似，nowdoc 结构也有相同的特征。
一个 nowdoc 结构也用和 heredocs 结构一样的标记 <<<， 但是跟在后面的标识符要用单引号括起来，即 <<<'EOT'。Heredoc 结构的所有规则也同样适用于 nowdoc 结构，尤其是结束标识符的规则。


spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.class.php';
});

为了实现向后兼容性，如果 PHP 5 在类中找不到 __construct() 函数并且也没有从父类继承一个的话，它就会尝试寻找旧式的构造函数，也就是和类同名的函数。因此唯一会产生兼容性问题的情况是：类中已有一个名为 __construct() 的方法却被用于其它用途时。

Trait 是为类似 PHP 的单继承语言而准备的一种代码复用机制。Trait 为了减少单继承语言的限制，使开发人员能够自由地在不同层次结构内独立的类中复用 method。Trait 和 Class 组合的语义定义了一种减少复杂性的方式，避免传统多继承和 Mixin 类相关典型问题。
Trait 和 Class 相似，但仅仅旨在用细粒度和一致的方式来组合功能。 无法通过 trait 自身来实例化。它为传统继承增加了水平特性的组合；也就是说，应用的几个 Class 之间不需要继承。
通过逗号分隔，在 use 声明列出多个 trait，可以都插入到一个类中。


如果两个 trait 都插入了一个同名的方法，如果没有明确解决冲突将会产生一个致命错误。

为了解决多个 trait 在同一个类中的命名冲突，需要使用 insteadof 操作符来明确指定使用冲突方法中的哪一个。

以上方式仅允许排除掉其它方法，as 操作符可以将其中一个冲突的方法以另一个名称来引入。
在本例中 Talker 使用了 trait A 和 B。由于 A 和 B 有冲突的方法，其定义了使用 trait B 中的 smallTalk 以及 trait A 中的 bigTalk。
Aliased_Talker 使用了 as 操作符来定义了 talk 来作为 B 的 bigTalk 的别名。



如果两个 trait 都插入了一个同名的方法，如果没有明确解决冲突将会产生一个致命错误。
为了解决多个 trait 在同一个类中的命名冲突，需要使用 insteadof 操作符来明确指定使用冲突方法中的哪一个。
以上方式仅允许排除掉其它方法，as 操作符可以将其中一个冲突的方法以另一个名称来引入。


如果 trait 定义了一个属性，那类将不能定义同样名称的属性，否则会产生一个错误。
如果该属性在类中的定义与在 trait 中的定义兼容（同样的可见性和初始值）则错误的级别是 E_STRICT，否则是一个致命错误。


PHP 7 开始支持匿名类。 匿名类很有用，可以创建一次性的简单对象。
匿名类
