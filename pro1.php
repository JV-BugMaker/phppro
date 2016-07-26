<?php

// $result = 1;
// $a = -1;
// if($a){
//   $result = 2;
// }
// echo $result;


// function test(){
//   $a=1;
//   $b=&$a;  //使得$a变量的ref——gc——count 1
//   //write on copy
//   echo (++$a)+(++$a),$b;
// }
// test();


// $a=1;
// echo (++$a)+(++$a);
// //换种写法就等同于
// $a=1;
// $a=++$a; //2
// $b=++$a; //3
// $a=$a+$b;//2+3=5


function test(){
 $a=1;
 $b=&$a;
 echo (++$a)+(++$a)+(++$a);
}
test();
