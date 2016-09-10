<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/9/10
 * Time: 下午9:48
 * json_encode 你不知道的部分
 */
$arr = array(1,2,3,4,5);


echo json_encode($arr);

//一般在使用json_encode的时候是不带第二个参数的  但是实际上存在第二个参数  看看php中 提高的参数有哪些选择
//json_last_error() 所返回的错误类型

JSON_ERROR_NONE(); //没有发生错误  PHP5.3.0
JSON_ERROR_DEPTH(); //达到了最大堆栈深度 PHP5.3.0
JSON_ERROR_STATE_MISMATCH(); //出现下溢或者模式匹配不对 PHP5.3.0
JSON_ERROR_CTRL_CHAR(); //控制字符错误 可能是编码不对 PHP5.3.0
JSON_ERROR_SYNTAX(); //语法错误 PHP5.3.0
JSON_ERROR_UTF8(); //异常的UTF-8字符 也许是因为不正确的编码  PHP5.3.1


JSON_HEX_TAG();//所有的 < 和 > 转换成 \u003C 和 \u003E。 自 PHP 5.3.0 起生效。
JSON_HEX_AMP(); //所有的 & 转换成 \u0026。 自 PHP 5.3.0 起生效。
JSON_HEX_APOS(); //所有的 ' 转换成 \u0027。 自 PHP 5.3.0 起生效。
JSON_HEX_QUOT(); //所有的 " 转换成 \u0022。 自 PHP 5.3.0 起生效。
JSON_FORCE_OBJECT(); //使一个非关联数组输出一个类（Object）而非数组。 在数组为空而接受者需要一个类（Object）的时候尤其有用。 自 PHP 5.3.0 起生效。
JSON_NUMERIC_CHECK(); //将所有数字字符串编码成数字（numbers）。 自 PHP 5.3.3 起生效。
JSON_BIGINT_AS_STRING();//将大数字编码成原始字符原来的值。 自 PHP 5.4.0 起生效。
JSON_PRETTY_PRINT();//用空白字符格式化返回的数据。 自 PHP 5.4.0 起生效。
JSON_UNESCAPED_SLASHES(); //不要编码 /。 自 PHP 5.4.0 起生效。
JSON_UNESCAPED_UNICODE();//以字面编码多字节 Unicode 字符（默认是编码成 \uXXXX）。 自 PHP 5.4.0 起生效。


