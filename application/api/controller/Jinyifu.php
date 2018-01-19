<?php
/**
 * @version  金易付加密 
 * @authors Mr.gao(928791694@qq.com)
 * @date    2017-09-29 16:03:05
 * @version $Bill$
 */
namespace app\api\controller;

use think\Loader;
use think\Config;

class Jinyifu
{
	
	protected $cipher;
    protected $mode ;
    protected $pad_method = NULL;
    protected $secret_key ='';
    protected $iv = '';
    public function __construct($secret_key){
    	$this->secret_key =$secret_key;
    	$this->cipher = MCRYPT_RIJNDAEL_128;
    	$this->mode =MCRYPT_MODE_ECB;
    	$this->iv ='';
    	$this->pad_method = 'pkcs5';
    }

    protected function pad_or_unpad($str, $ext)
    {
        if (is_null($this->pad_method)) {
            return $str;
        } else {
            $func_name = __CLASS__ . '::' . $this->pad_method . '_' . $ext . 'pad';
            if (is_callable($func_name)) {
                $size = mcrypt_get_block_size($this->cipher, $this->mode);
                return call_user_func($func_name, $str, $size);
            }
        }

        return $str;
    }
    protected function pad($str)
    {
        return $this->pad_or_unpad($str, '');
    }
    protected function unpad($str)
    {
        return $this->pad_or_unpad($str, 'un');
    }
    public function encrypt($str)
    {
        $str = $this->pad($str);
        $td = mcrypt_module_open($this->cipher, '', $this->mode, '');
        if (empty($this->iv)) {
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        } else {
            $iv = $this->iv;
        }
        mcrypt_generic_init($td, $this->secret_key, $iv);
       
        $cyper_text = mcrypt_generic($td, $str);
        //$rt = base64_encode($cyper_text);
        $rt = bin2hex($cyper_text);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $rt;
    }
    public function decrypt($str)
    {
        $td = mcrypt_module_open($this->cipher, '', $this->mode, '');
        if (empty($this->iv)) {
            $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        } else {
            $iv = $this->iv;
        }
        mcrypt_generic_init($td, $this->secret_key, $iv);
        //$decrypted_text = mdecrypt_generic($td, self::hex2bin($str));
        $decrypted_text = mdecrypt_generic($td, base64_decode($str));
        $rt = $decrypted_text;
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $this->unpad($rt);
    }
    public static function hex2bin($hexdata)
    {
        $bindata = '';
        $length = strlen($hexdata);
        for ($i = 0; $i < $length; $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }
    public static function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - strlen($text) % $blocksize;
        return $text . str_repeat(chr($pad), $pad);
    }
    public static function pkcs5_unpad($text)
    {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
}