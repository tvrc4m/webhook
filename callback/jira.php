<?php

error_reporting(E_ALL ^ E_NOTICE);

define('ROOT', dirname(__dir__));
define('BASEURL', 'http://webhook.vrcdkj.cn');

$str = file_get_contents('php://input');

if(empty($str)) return false;

@file_put_contents('/tmp/jira.log', $str.PHP_EOL,FILE_APPEND);

$project=$_GET['project'];
$access_token=$_GET['access_token'];

$post=json_decode($str,true);

$action=$post['webhookEvent'];

include_once(ROOT.'/jira/message.php');
include_once(ROOT.'/dingtalk/notify.php');

$jira_message=new JiraMessage($project,$post);
$dingtalk_notify=new DingtalkNotify($access_token);

switch ($action) {

    case 'jira:issue_created':
    {
        $result=$dingtalk_notify->issueCreate($jira_message);
        
        break;   
    }

    case 'jira:issue_updated':
    {
        
        if($jira_message->is_resolved){

            $result=$dingtalk_notify->issueResolved($jira_message);
        }else if($jira_message->is_reopened){

            $result=$dingtalk_notify->issueReopened($jira_message);
        }else{

            $result=$dingtalk_notify->issueUpdated($jira_message);    
        }
        
        // 当issue由staging测试及开发完成时
        // 目前只支持php项目
        if ($project=='php' && ($jira_message->test_staging || $jira_message->test_dev)) {
            
            include_once(ROOT.'/gitlab/api.php');

            if($jira_message->test_staging){

                $dest_branch='develop';
            }else if($jira_message->test_dev){

                $dest_branch='app';
            }

            $gitlab_api=new GitlabApi();

            $src_branch=$jira_message->getIssueNumber();
            $parent_branch=$jira_message->getIssueParentNumber();
            $issue_title=$jira_message->getIssueTitle();
            $operator=$jira_message->getIssueOperator();
            $title=' merge '.$src_branch.' to '.$dest_branch;

            $find_branch=true;
            // 先查看parent branch是否存在
            if($parent_branch){

                $parentBranchInfo=$gitlab_api->getBranch($parent_branch);

                if(empty($parentBranchInfo) || isset($parentBranchInfo['message'])){

                    $parentBranchInfo=$gitlab_api->getBranch(strtolower($src_branch));

                    if(empty($parentBranchInfo) || isset($parentBranchInfo['message'])){

                        $dingtalk_notify->notifyText("{$src_branch}分支不存在","{$issue_title}\n\n{$src_branch}分支不存在");

                        $find_branch=false;
                    }else{

                        $src_branch=strtolower($src_branch);
                    }
                }
            }
            // 如果没有parent branch 则看issue branch
            if(!$find_branch){

                $branchInfo=$gitlab_api->getBranch($src_branch);

                if(empty($branchInfo) || isset($branchInfo['message'])){

                    $branchInfo=$gitlab_api->getBranch(strtolower($src_branch));

                    if(empty($branchInfo) || isset($branchInfo['message'])){

                        $dingtalk_notify->notifyText("{$src_branch}分支不存在","{$issue_title}\n\n{$src_branch}分支不存在");

                        exit(0);
                    }else{

                        $src_branch=strtolower($src_branch);
                    }
                }
            }

            // 发起请求
            $result=$gitlab_api->createMergeRequest($src_branch,$dest_branch,$operator.$title);

            if(empty($result)){

                $dingtalk_notify->notifyText($title,$operator.$title."合并请求失败");
            }elseif($result['message']){
                // 删除合并请求
                $gitlab_api->delMergeRequest($result['id']);

                $dingtalk_notify->notifyTextUrl($operator.$title,$result['message'],$result['web_url'],BASEURL.'/git-icon.png');
            }else{
                // 接受合并请求
                $response=$gitlab_api->acceptMergeRequest($result['id']);

                if(empty($response)){

                    $dingtalk_notify->notifyText($title,$operator.$title.'合并请求失败');
                }elseif($response['message']){
                    @file_put_contents('/tmp/merged_code.log', $gitlab_api->http_code,FILE_APPEND);
                    if($gitlab_api->http_code==406){
                        $message="存在冲突,需要开发者手动合并到".$dest_branch."解决冲突然后提交";
                    }elseif($gitlab_api->http_code==405){
                        $message="该分支已合并到".$dest_branch."或者该分支不存在";
                    }elseif($gitlab_api->http_code==401){
                        $message="没有权限进行合并";
                    }

                    $dingtalk_notify->notifyTextUrl($operator.$title,$message,$result['web_url'],BASEURL.'/git-icon.png');
                }else{
                    // 采用gitlab webhook通知
                    // 合并成功,记录这次分支合并                    
                    $log=$src_branch.'$$'.$result['id'].'$$'.$result['message'].'$$'.$result['web_url']."\n";
                    if($dest_branch=='develop'){
                        @file_put_contents('/var/log/develop_merged.log',$log,FILE_APPEND);    
                    }elseif($dest_branch=='app'){
                        @file_put_contents('/var/log/app_merged.log',$log,FILE_APPEND);
                    }
                }
            }
        }

        break;
    }
}
