<?php
//明文 -->(key+向量(iv))--> 密文，所以一定要保证你的php和Java的key和向量保持一致，一般key可以很容易的保持一致，但是向量一般容易被忽视，你需要知道Java那一端的向量是怎么生成的。
class Phpaes
{
    public static function AesEncrypt($clear_text, $key = ''){
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
}
$id = 10; 
$cipher = Phpaes::AesEncrypt($id, 'dsffffsdfsdfsdfs');
echo $cipher;