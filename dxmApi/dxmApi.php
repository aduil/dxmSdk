<?php
namespace dxmApi;

class dxmApi
{    
    private static $_instance = null;
    
    private $debugMode = false;
    
    private $url = "https://aduil.com/dxm/admin/addSms.php";
    
    private $token = null;
    private $mobile = null;
    private $tid = null;  // template id
    private $sign = null;  // sign at front of a msg
    private $params = null;  // params depends on tid, see docs
    
    private function __construct($token)
    {
        $this->token = $token;
        // Record ip of visitor
        $this->ip = $this->_getUserIP();
    }
    public static function getInstance($token)
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self($token);
        }
        return self::$_instance;
    }
    public function __clone()
    {
    }
    
    public function debugMode($cond)
    {
        if($cond) {
            $this->debugMode = true;
            echo "<pre>";
        } else {
            $this->debugMode = false;
        }
        return $this;
    }

    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }
    public function setTid($tid)
    {
        $this->tid = $tid;
        return $this;
    }
    
    public function setSign($sign)
    {
        $this->sign = $sign;
        return $this;
    }
    
    public function setParams($params)
    {
        $this->params = json_encode($params, 256);
        return $this;
    }
    
    public function sendMsg()
    {
        $this->data = array(
            'ip' => $this->ip,
            'mobile' => $this->mobile,
            'tid' => $this->tid,
            'token' => $this->token,
            'params' => $this->params
        );
        $r = $this->_checkPostData();
        // unnecessary fields
        $this->data['sign'] = $this->sign;
        if($r === true) {
            return $this->_postByCurl($this->url, $this->data);
        } else {
            return $r;
        }
    }
    
    private function _postByCurl($url, $postData)
    {
        if($this->debugMode) {
            var_dump($url, $postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);  // POST Method
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // POST Data
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    
    /**
     * Filter invalid data
     */
    private function _checkPostData()
    {
        foreach ($this->data as $k => $v) {
            if(empty($v)) {
                return 'invalid ' . $k;
            }
        }
        preg_match("#^1[3578][0-9]{9}$#", $this->data['mobile'], $match);
        if (count($match) == 0 || $match[0] == '' || strlen($match[0]) != 11) {
            return 'invalid mibile';
        }
        
        return true;
    }
    
    private function _getUserIP()
    {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }
}


