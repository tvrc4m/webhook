<?php

error_reporting(E_ALL ^ E_NOTICE);

define('ROOT', dirname(__dir__));
define('BASEURL', 'http://webhook.vrcdkj.cn');

$str = file_get_contents('php://input');

if(empty($str)) return false;

@file_put_contents('/tmp/gitlab.log', $str.PHP_EOL,FILE_APPEND);

$project=$_GET['project'];

$access_token = $_SERVER['HTTP_X_GITLAB_TOKEN'];

$post=json_decode($str,true);

$action=$post['object_kind'];

include_once(ROOT.'/gitlab/message.php');
include_once(ROOT.'/dingtalk/notify.php');

$gitlab_message=new GitlabMessage($project,$post);
$dingtalk_notify=new DingtalkNotify($access_token);
switch ($action) {

    case 'push':
    {
        if(!$gitlab_message->isDeleted()){
            $result=$dingtalk_notify->gitPush($gitlab_message);

            $branch_name=$gitlab_message->getBranchName();

            if(!in_array($branch_name, ['master','app','develop'])){

                $develop_merged_content=file_get_contents(ROOT."/log/develop_merged.log");

                $develop_merged_list=array_filter(explode("\n", $develop_merged_content));

                if(in_array($branch_name, $develop_merged_list)){
                    // 发送请求合并的通知
                    $dingtalk_notify->reqMerge($gitlab_message);
                }
            }
        }
        break;   
    }
    case 'merge_request':
    {
        $dingtalk_notify->gitMerge($gitlab_message);
    }
}
