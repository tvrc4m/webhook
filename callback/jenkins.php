<?php

$get=$_GET;

$access_token=$get['access_token'];
$env=$get['env'];
$branch=$get['branch'];

if(empty($access_token)) return false;

$branch=str_replace('origin/','', $branch);

$dingtalk_notify=new DingtalkNotify($access_token);

$dingtalk_notify->deploySuccess($env,$branch);