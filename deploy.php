<?php

$env=$_GET['env'];
$branch=$_GET['branch'];
$access_token=$_GET['access_token'];

if(empty($env) || empty($branch)) exit('缺少参数');

define('ROOT', __DIR__);

include_once(ROOT.'/jenkins/cli.php');
include_once(ROOT.'/dingtalk/notify.php');

$cli=new JenkinsCli();
$dingtalk_notify=new DingtalkNotify($access_token);

$result=$cli->deploy($env,$branch);

if($env!='dev' && $env!='staging' && $env!='webhook' && $env!='doc'){
    
    $dingtalk_notify->deployStart($env,$branch);    
}

print_r($result);