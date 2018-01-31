<?php

error_reporting(E_ALL ^ E_NOTICE);

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

$access_token='699b16574ca2c3c851095fe5555fe5b878a5d96e4303d0a5ce8a1d991e3b1166';

include_once(ROOT.'/dingtalk/notify.php');

$dingtalk_notify=new DingtalkNotify($access_token);

if($week!=1){

    $developers=['李硕','武超','娟娟','阿勇','郑生齐','刘艳梅','许俊帅'];

    $count=count($developers);

    $logfile="/var/log/meeting.log";

    $pre=@file_get_contents($logfile);

    $pre=intval($pre);

    $current=($count<=$pre+1)?0:$pre+1;

    $dingtalk_notify->notifyText("php晨会提醒","NOTICE: 10am开晨会,实习leader: ".$developers[$current],true);

    @file_put_contents($logfile, $current);
}else{

    $dingtalk_notify->notifyText("php晨会提醒","NOTICE: 10am周一例会",true);
}



