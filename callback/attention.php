<?php

define('ROOT', dirname(__dir__));
date_default_timezone_set("Asia/Shanghai");

$week=date('w');
$month=date('m');
$day=date('d');

// 周六和周日直接返回
if($week==0 || $week==6) return;
// 部分节假日
if($month==2 && in_array($day,[15,16,19,20,21])) return;
if($month==4 && in_array($day,[5,6,30])) return;
if($month==5 && in_array($day,[1])) return;
if($month==6 && in_array($day,[18])) return;
if($month==9 && in_array($day,[24])) return;

$access_token='72f583366e685e7de36cd25715d77e3cc5bb07a512bb5ebd5e8da9ddb0c32b07';

include_once(ROOT.'/dingtalk/notify.php');

$dingtalk_notify=new DingtalkNotify($access_token);

$developers=['郑生齐','肖阿勇','王娟娟','李硕','刘艳梅','武超','许俊帅','魏山'];

$count=count($developers);

$logfile="/var/log/attention.log";

$pre=@file_get_contents($logfile);

$pre=intval($pre);

$current=($count<=$pre+1)?0:$pre+1;

$dingtalk_notify->notifyText('今日DEV环境负责人','今日DEV环境专人,负责定位问题,由问题生成者解决: '.$developers[$current]);

file_put_contents($logfile, $current);

