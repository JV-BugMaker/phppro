<?php

function getByMoney($money)
{
  return array(floor($money/2), $money%2);
}

function getByGaizi($gaizi)
{
  return array(floor($gaizi/4), $gaizi%4);
}

function getByPingzi($pingzi)
{
  return array(floor($pingzi/2), $pingzi%2);
}

function main ($money, $pingzi = 0, $gaizi = 0, $count = 0)
{
  if ($money >= 2) {
    $return = getByMoney($money);
    $count = $count + $return[0];
    $gaizi = $gaizi + $return[0];
    $pingzi = $pingzi + $return[0];
    $money = $return[1];
  }
  if ($pingzi >= 2) {
    $return = getByPingzi($pingzi);
    
    $gaizi = $gaizi + $return[0];
    $pingzi = $return[1] + $return[0];
  }
  if ($gaizi >=4) {
    $return = getByGaizi($gaizi);
    $count = $count + $return[0];
    $pingzi = $pingzi + $return[0];
    $gaizi = $return[1] + $return[0];
  }
  if ($money < 2 && $gaizi < 4 && $pingzi < 2) {
  //var_dump('money:'.$money, 'gaizi:'.$gaizi, 'pingzi:'.$pingzi, 'count:'.$count);exit;
    return $count;
  } else {
    return main($money, $pingzi, $gaizi, $count);
  }
}
var_dump(main(10));
