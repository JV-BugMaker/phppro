<?php
// $str = "\u003cscript&gtalert('js')\u003c\/script&gt";
if($_GET['name']){
  echo $_GET['name'];
}else{
  echo "<form action='' method='get'><input name='name' type='text'/><input type='submit' name='asd' value='adasd'/></form>";

}
