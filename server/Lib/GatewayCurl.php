<?php
namespace Lib;
class GatewayCurl {
    
    public static function getNonceStr($length = 27) {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars) -1 ), 1);
        }
        return $str;
    }
    
    public static function send($url, $params) {
        $time = time();

        ksort($params);
        $nonce = self::getNonceStr();
        
        $params['nonce'] = $nonce;
        
        $str = http_build_query($params) . '&timestamp=' . $time;
        
        $sign = strtolower(hash_hmac('sha256', $str, $time));
        $len = strlen($sign);
        
        $params['key'] = $time;
        $params['sign'] = substr($sign, 0, $len - 27) . $nonce; //把nonce代替截取掉的27个字符
        $params['nonce'] = substr($sign, $len - 27) . self::getNonceStr(mt_rand(0, 6)); //从sign截取最后27个字符伪装nonce
        
        $res = ['success' => false];

        try {
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            $rep = $client->postJSON(json_encode($params))->getBody();
            $client = null;
            if(empty($rep)) return $res;
            $rep = json_decode($rep, true);
            if(empty($rep)) return $res;
            
            return $rep;
        } catch (\Exception $e) {
            $res['msg'] = $e->getMessage();
        }
        
        return $res;
    }
}