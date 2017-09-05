<?php

define('ROOT', dirname(__dir__));

$str = file_get_contents('php://input');

$str='{"transition":{"workflowId":10482,"workflowName":"Kf Php","transitionId":41,"transitionName":"开发完成","from_status":"开发中","to_status":"null"},"comment":"","user":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=admin","name":"admin","emailAddress":"chengchen@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=48"},"displayName":"admin","active":true},"issue":{"id":"10382","self":"http://101.201.234.44:8080/rest/api/2/issue/10382","key":"KF-202","fields":{"progress":{"progress":0,"total":0},"summary":"定制Jira的钉钉机器人，实现更多更细粒度的通知机制","timetracking":{},"issuetype":{"self":"http://101.201.234.44:8080/rest/api/2/issuetype/2","id":"2","description":"对产品提出新功能需求","iconUrl":"http://101.201.234.44:8080/images/icons/issuetypes/newfeature.png","name":"新需求","subtask":false},"votes":{"self":"http://101.201.234.44:8080/rest/api/2/issue/KF-202/votes","votes":0,"hasVoted":false},"resolution":{"self":"http://101.201.234.44:8080/rest/api/2/resolution/1","id":"1","description":"报告缺陷已经修复或已经被列入修复计划。","name":"已修复"},"fixVersions":[],"resolutiondate":"2017-09-05T09:16:28.245+0800","timespent":null,"creator":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=admin","name":"admin","emailAddress":"chengchen@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=48"},"displayName":"admin","active":true},"reporter":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=admin","name":"admin","emailAddress":"chengchen@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=48"},"displayName":"admin","active":true},"aggregatetimeoriginalestimate":null,"created":"2017-09-01T16:13:22.000+0800","updated":"2017-09-03T15:54:35.000+0800","description":"自带的jira机器人不能满足需求，需要重写定制化更好的通知机制\r\n\r\n参考链接：\r\n钉钉: https://open-doc.dingtalk.com/docs/doc.htm?treeId=257&articleId=105735&docType=1","priority":{"self":"http://101.201.234.44:8080/rest/api/2/priority/3","iconUrl":"http://101.201.234.44:8080/images/icons/priorities/major.png","name":"一般","id":"3"},"duedate":null,"customfield_10001":null,"issuelinks":[],"customfield_10004":"356","watches":{"self":"http://101.201.234.44:8080/rest/api/2/issue/KF-202/watchers","watchCount":1,"isWatching":true},"worklog":{"startAt":0,"maxResults":20,"total":0,"worklogs":[]},"customfield_10000":null,"subtasks":[],"status":{"self":"http://101.201.234.44:8080/rest/api/2/status/10004","description":"","iconUrl":"http://101.201.234.44:8080/images/icons/status_generic.gif","name":"开发中","id":"10004","statusCategory":{"self":"http://101.201.234.44:8080/rest/api/2/statuscategory/4","id":4,"key":"indeterminate","colorName":"yellow","name":"In Progress"}},"customfield_10006":null,"labels":[],"customfield_10005":null,"workratio":-1,"assignee":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=%E9%AD%8F%E5%B1%B1","name":"魏山","emailAddress":"weishan@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/554098f661258e0d2b9fe73dadc678c1?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/554098f661258e0d2b9fe73dadc678c1?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/554098f661258e0d2b9fe73dadc678c1?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/554098f661258e0d2b9fe73dadc678c1?d=mm&s=48"},"displayName":"魏山","active":true},"attachment":[],"aggregatetimeestimate":null,"project":{"self":"http://101.201.234.44:8080/rest/api/2/project/10002","id":"10002","key":"KF","name":"看法PHP","avatarUrls":{"16x16":"http://101.201.234.44:8080/secure/projectavatar?size=xsmall&pid=10002&avatarId=10011","24x24":"http://101.201.234.44:8080/secure/projectavatar?size=small&pid=10002&avatarId=10011","32x32":"http://101.201.234.44:8080/secure/projectavatar?size=medium&pid=10002&avatarId=10011","48x48":"http://101.201.234.44:8080/secure/projectavatar?pid=10002&avatarId=10011"}},"environment":null,"timeestimate":null,"customfield_10014":null,"lastViewed":"2017-09-05T09:16:28.235+0800","aggregateprogress":{"progress":0,"total":0},"customfield_10015":null,"customfield_10012":null,"customfield_10013":null,"components":[],"customfield_10010":null,"comment":{"startAt":0,"maxResults":0,"total":0,"comments":[]},"timeoriginalestimate":null,"customfield_10011":null,"aggregatetimespent":null}},"timestamp":1504574188244}';

if(empty($str)) return false;

@file_put_contents('/tmp/jira.log', $str.PHP_EOL,FILE_APPEND);

$project=$_GET['project'];
$access_token=$_GET['access_token'];

$access_token='11111';
$project='php';

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

    // case 'jira:issue_updated':
    // {
    //     $result=$dingtalk_notify->issueUpdated($jira_message);

    //     break;
    // }
}

if($jira_message->isTransition()){

    if($jira_message->IsIssueResolved()){
        
        $result=$dingtalk_notify->issueResolved($jira_message);
    }else if($jira_message->IsIssueReopened()){

        $result=$dingtalk_notify->issueReopened($jira_message);
    }
}
