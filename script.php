<?php

$script=$_GET['script'];
$ding_array = unserialize($script);
$access_token=$ding_array['access_token'] ? $ding_array['access_token'] : '0066be0fc686e9f11e20e28082f76acf9659e7a1d5b5065455403ae8b536b3a8';

if(empty($ding_array['name']) || empty($ding_array['run_time'])) exit('缺少参数');

define('ROOT', __DIR__);

include_once(ROOT.'/jenkins/cli.php');
include_once(ROOT.'/dingtalk/notify.php');

$dingtalk_notify=new DingtalkNotify($access_token);

$dingtalk_notify->scriptSuccess($ding_array);    
