<?php
//finally


try{
    //尝试做某件危险的事情
}catch(Exception $e){
  //throw
}finally{
  //不管上面处理结果是怎么样的 最终都会回归到这边 因此 我这边可以对上面处理的信息 进行总结
  //这个设计比较有意思 但是 用到的地方的确不怎么多 简洁了代码
}


try{
    do_something_danger();
}catch(Exception $e){
    clearDanger();
    throw $e->getMessage();
}

clearDanger();
//以上代码可以更简单一些  finally是规定必须执行到的

try{
    do_something_danger();
}finally{
    clearDanger();
}
