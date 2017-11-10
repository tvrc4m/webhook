<?php

$script=$_GET['script'];
$ding_array = unserialize($script);
$access_token='c411aafaaadedc846a9c3aa498e6c38c55434406387cb5e4d555b19dd37d39da';

if(empty($ding_array['name']) || empty($ding_array['run_time'])) exit('缺少参数');

define('ROOT', __DIR__);

include_once(ROOT.'/jenkins/cli.php');
include_once(ROOT.'/dingtalk/notify.php');

$dingtalk_notify=new DingtalkNotify($access_token);

$dingtalk_notify->scriptSuccess($ding_array);    
