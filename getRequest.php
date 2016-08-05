<?php
ob_start();
$header = getallheaders();
$dh = opendir('./apk');
$arr_dir = array();
while( ($dir = readdir($dh)) !== false ) {
    if ($dir == "." || $dir == "..") {
            continue;
    }
    $arr_dir[] = substr($dir,1);

}

usort($arr_dir,"sortByVer");
$arr_dir = array_reverse($arr_dir);

if(version_compare($arr_dir[0],substr($header['Ver'],1))>0 && $header['Rate'] && randUp($header['Rate'])){
    //都符合条件就更新 
    //由于引入文件 会输出到缓冲区 所以需要清空缓冲区 避免多余的信息输出
    $json = include('./apk/v'.$arr_dir[0].'/config.json');
    ob_clean();
    $response = json_encode(array(
        "code"=>1000,
        "versionConfig"=>json_decode($json,true)
    ));
    $response = AesEncrypt($response,"1234567812345678");
    echo $response;
}




function sortByVer($x,$y){
    return version_compare($x,$y);
}

function randUp($max){
    $rand_num = rand(1,100);
    if($rand_num<=$max){
        return true;
    }else{
        return false;
    }
}

function AesEncrypt($clear_text, $key = ''){
        $clear_text = trim($clear_text);
        if($clear_text == ''){
            return ''; 
        }   
        $m = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = $key;
        mcrypt_generic_init($m, $key, $iv);
        //下面这3行很关键
        $blocksize = 16; 
        $pad = $blocksize - (strlen($clear_text) % $blocksize);
        $clear_text = $clear_text . str_repeat(chr($pad), $pad);
        
        $data = mcrypt_generic($m, $clear_text);
        mcrypt_generic_deinit($m);
        mcrypt_module_close($m);
        return base64_encode($data);
}   