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
        $result=$dingtalk_notify->issueUpdated($jira_message);

        if ($jira_message->test_staging) {
            
            include_once(ROOT.'/gitlab/api.php');

            $gitlab_api=new GitlabApi();

            $src_branch=$jira_message->getIssueNumber();
            $operator=$jira_message->getIssueOperator();
            $title=' merge '.$src_branch.' to develop';
            // 发起请求
            $result=$gitlab_api->createMergeRequest($src_branch,'develop',$operator.$title);

            if(empty($result)){

                $dingtalk_notify->notifyText($title,$operator.' 发起合并请求失败:'.$title);
            }elseif($result['message']){

                $dingtalk_notify->notifyTextUrl($title,$operator.'发起合并请求失败:'.$result['message'],$result['web_url'],BASEURL.'/git-icon.png');
            }else{
                // 接受合并请求
                $response=$gitlab_api->acceptMergeRequest($result['id']);

                if(empty($response)){

                    $dingtalk_notify->notifyText($title,$operator.'接受合并请求失败:'.$title);
                }elseif($response['message']){

                    $dingtalk_notify->notifyTextUrl($title,$operator.'接受合并请求失败:'.$response['message'],$result['web_url'],BASEURL.'/git-icon.png');
                }else{

                    // 采用gitlab webhook通知
                }
            }
        }

        break;
    }
}

if($jira_message->isTransition()){

    if($jira_message->IsIssueResolved()){
        
        $result=$dingtalk_notify->issueResolved($jira_message);
    }else if($jira_message->IsIssueReopened()){

        $result=$dingtalk_notify->issueReopened($jira_message);
    }
}
