<?php

$title=$_GET['title'];
$text=$_GET['text'];
$access_token='699b16574ca2c3c851095fe5555fe5b878a5d96e4303d0a5ce8a1d991e3b1166';

if(empty($title) || empty($text)) exit('缺少参数');

define('ROOT', __DIR__);

include_once(ROOT.'/jenkins/cli.php');
include_once(ROOT.'/dingtalk/notify.php');

$dingtalk_notify=new DingtalkNotify($access_token);

$dingtalk_notify->deployStart($title,$text);    

print_r($result);