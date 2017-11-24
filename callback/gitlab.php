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

            $branch_name=$gitlab_message->getBranchName();

            $update_merge_develop=$update_merge_app=0;

            if(!in_array($branch_name, ['master','app','develop'])){
                // 检测develop分支
                $develop_merged_content=@file_get_contents("/var/log/develop_merged.log");

                $develop_merged_list=array_filter(explode("\n", $develop_merged_content));

                $branch_list=[];

                foreach ($develop_merged_list as $list) {
                    
                    list($branch,$merge_id)=array_filter(explode('$$', $list));

                    $branch_list[]=$branch;
                }

                if(in_array($branch_name, $branch_list)){
                    // 发送请求合并的通知
                    $update_merge_develop=1;
                    // $resp=$dingtalk_notify->reqMerge($gitlab_message);
                    // print_r($resp);
                }
                // 检测app分支
                $app_merged_content=@file_get_contents("/var/log/app_merged.log");

                $app_merged_list=array_filter(explode("\n", $app_merged_content));

                $branch_list=[];

                foreach ($app_merged_list as $list) {
                    
                    list($branch,$merge_id)=array_filter(explode('$$', $list));

                    $branch_list[]=$branch;
                }

                if(in_array($branch_name, $branch_list)){
                    // 发送请求合并的通知
                    $update_merge_app=1;
                    // $resp=$dingtalk_notify->reqMerge($gitlab_message);
                    // print_r($resp);
                }
            }

            $result=$dingtalk_notify->gitPush($gitlab_message,$update_merge_develop,$update_merge_app);
        }
        break;   
    }
    case 'merge_request':
    {
        $dingtalk_notify->gitMerge($gitlab_message);
    }
}
