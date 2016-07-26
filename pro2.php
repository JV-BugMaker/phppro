<?php
/*
*摘要：编程求解：啤酒2元钱1瓶；4个瓶盖可换1瓶，2个空瓶可换1瓶。 问：10元钱可以喝几瓶？
*
 */
$monery = 10 ;
$price = 2;
define("RATE",4);
define("RATE2",2);

$num = $monery/$price;
$now_top = $now_body = $num;


while ($now_body >=RATE2 || $now_top >=RATE) {
  # code...
      if($now_body>=RATE2){
          bodyToBeer();
      }
      if ($now_top>=RATE) {
        # code...
          topToBeer();
      }
}

var_dump($num,$now_top,$now_body);



function topToBeer ()
{
    global $now_top,$num,$now_body;
    $rate = intval($now_top / RATE );
    $now_top = $now_top % RATE;
    $num += $rate ;
    $now_top += $rate ;
    $now_body += $rate ;
}


function bodyToBeer()
{
    global $now_body,$now_top,$num;
    $rate = intval($now_body / RATE2) ;
    $now_body = $now_body % RATE2 ;
    $num += $rate;
    $now_top += $rate;
    $now_body += $rate;
}
