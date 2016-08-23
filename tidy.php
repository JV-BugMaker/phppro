<?php
/**
 * Created by PhpStorm.
 * User: JV
 * Date: 16/8/24
 * Time: 上午12:58
 * theme:tidy 对html进行标签处理
 */

ob_start();
echo <<<HTML
<html>
   <head>
    <title>tidy</title>
</head>
<body>
    <p>test</p>
</body>
</html>

HTML;

$buffer = ob_get_contents();
$config = array(
    'indent'=>TRUE,
    'output-xhtml'=>TRUE,
    'wrap'=>200
);
$html = tidy_parse_string($buffer,$config,'UTF8');
$html->cleanRepair();
$html= $html->value;
print_r($html);
?>