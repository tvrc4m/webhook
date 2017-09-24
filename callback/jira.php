<?php

define('ROOT', dirname(__dir__));

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
            $title='merge '.$src_branch.' to develop';

            $result=$gitlab_api->createMergeRequest($src_branch,'develop',$title);

            if(empty($result)){

                $dingtalk_notify->notifyText($title,$operator.' 发起合并请求失败:'.$title);
            }elseif($result['message']){

                $dingtalk_notify->notifyTextUrl($title,$operator.' 发起合并请求失败:'.$result['message'],'查看详情',$result['web_url']);
            }else{
                // 接受合并请求
                $response=$gitlab_api->acceptMergeRequest($result['id']);

                if(empty($response)){

                    $dingtalk_notify->notifyText($title,$operator.'接受合并请求失败:'.$title);
                }elseif($result['message']){

                    $dingtalk_notify->notifyTextUrl($title,$operator.'接受合并请求失败:'.$result['message'],'查看详情',$result['web_url']);
                }else{

                    $dingtalk_notify->notifyTextUrl($title,$result['title']."\n\n> 合并成功",'查看详情',$result['web_url']);
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
