<?php

require_once "dxmApi/dxmApi.php";

use dxmApi\dxmApi;

class dxmTest
{
    public function __construct()
    {
    }
    
    public function init()
    {
        // To get a valid token, conact liuda please
        $token = '123456';
        $dxm = dxmApi::getInstance($token);
        $dxm->debugMode(true)
            ->setSign('短信喵')
            ->setMobile('15957186303')
            ->setTid(3)
            ->setParams(array(
                'code' => $this->getCode(),
                'website' => '刘大短信喵',
                'username' => '大明',
            ));
        $r = $dxm->sendMsg();
        print_r($r);
    }
    
    private function getCode()
    {
        return rand(1000, 9999);
    }
}

$test = new dxmTest();

$test->init();

