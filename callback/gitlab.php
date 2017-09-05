<?php

define('ROOT', dirname(__dir__));

$str = file_get_contents('php://input');

if(empty($str)) return false;

@file_put_contents('/tmp/gitlab.log', $str.PHP_EOL,FILE_APPEND);

$project=$_GET['project'];
$access_token=$_GET['access_token'];

$post=json_decode($str,true);

$action=$post['object_kind'];

include_once(ROOT.'/gitlab/message.php');
include_once(ROOT.'/dingtalk/notify.php');

$gitlab_message=new GitlabMessage($project,$post);
$dingtalk_notify=new DingtalkNotify($access_token);

switch ($action) {

    case 'push':
    {
        $result=$dingtalk_notify->gitPush($gitlab_message);
        
        break;   
    }
}
