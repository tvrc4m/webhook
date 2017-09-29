<?php

error_reporting(E_ALL ^ E_NOTICE);

define('ROOT', dirname(__dir__));

$access_token='699b16574ca2c3c851095fe5555fe5b878a5d96e4303d0a5ce8a1d991e3b1166';

include_once(ROOT.'/dingtalk/notify.php');

$dingtalk_notify=new DingtalkNotify($access_token);

$dingtalk_notify->notifyText("php晨会提醒","NOTICE: 10am开晨会 ",true);