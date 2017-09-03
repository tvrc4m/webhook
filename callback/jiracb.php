<?php

define('ROOT', dirname(__dir__));

$str = file_get_contents('php://input');

$str='{"webhookEvent":"jira:issue_created","user":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=admin","name":"admin","emailAddress":"chengchen@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=48"},"displayName":"admin","active":true},"issue":{"id":"10403","self":"http://101.201.234.44:8080/rest/api/2/issue/10403","key":"KANFA-12","fields":{"progress":{"progress":0,"total":0},"summary":"创建issue通知","timetracking":{},"issuetype":{"self":"http://101.201.234.44:8080/rest/api/2/issuetype/1","id":"1","description":"导致产品无法正常运行的故障","iconUrl":"http://101.201.234.44:8080/images/icons/issuetypes/bug.png","name":"缺陷","subtask":false},"votes":{"self":"http://101.201.234.44:8080/rest/api/2/issue/KANFA-12/votes","votes":0,"hasVoted":false},"resolution":null,"fixVersions":[],"resolutiondate":null,"timespent":null,"creator":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=admin","name":"admin","emailAddress":"chengchen@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=48"},"displayName":"admin","active":true},"reporter":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=admin","name":"admin","emailAddress":"chengchen@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=48"},"displayName":"admin","active":true},"aggregatetimeoriginalestimate":null,"updated":"2017-09-03T15:49:56.610+0800","created":"2017-09-03T15:49:56.610+0800","description":"创建","priority":{"self":"http://101.201.234.44:8080/rest/api/2/priority/3","iconUrl":"http://101.201.234.44:8080/images/icons/priorities/major.png","name":"一般","id":"3"},"duedate":null,"customfield_10001":null,"issuelinks":[],"customfield_10004":"373","watches":{"self":"http://101.201.234.44:8080/rest/api/2/issue/KANFA-12/watchers","watchCount":0,"isWatching":false},"worklog":{"startAt":0,"maxResults":20,"total":0,"worklogs":[]},"customfield_10000":null,"subtasks":[],"status":{"self":"http://101.201.234.44:8080/rest/api/2/status/1","description":"提交的问题还没有开始解决","iconUrl":"http://101.201.234.44:8080/images/icons/statuses/open.png","name":"开放","id":"1","statusCategory":{"self":"http://101.201.234.44:8080/rest/api/2/statuscategory/2","id":2,"key":"new","colorName":"blue-gray","name":"New"}},"customfield_10006":null,"labels":[],"customfield_10005":null,"workratio":-1,"assignee":{"self":"http://101.201.234.44:8080/rest/api/2/user?username=admin","name":"admin","emailAddress":"chengchen@kanfanews.com","avatarUrls":{"16x16":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=16","24x24":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=24","32x32":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=32","48x48":"http://www.gravatar.com/avatar/f83028a7857d0bd5ff1d51b04e32c696?d=mm&s=48"},"displayName":"admin","active":true},"attachment":[],"aggregatetimeestimate":null,"project":{"self":"http://101.201.234.44:8080/rest/api/2/project/10000","id":"10000","key":"KANFA","name":"看法新闻","avatarUrls":{"16x16":"http://101.201.234.44:8080/secure/projectavatar?size=xsmall&pid=10000&avatarId=10011","24x24":"http://101.201.234.44:8080/secure/projectavatar?size=small&pid=10000&avatarId=10011","32x32":"http://101.201.234.44:8080/secure/projectavatar?size=medium&pid=10000&avatarId=10011","48x48":"http://101.201.234.44:8080/secure/projectavatar?pid=10000&avatarId=10011"}},"versions":[],"environment":null,"timeestimate":null,"customfield_10014":null,"aggregateprogress":{"progress":0,"total":0},"lastViewed":null,"customfield_10015":null,"customfield_10012":null,"customfield_10013":null,"components":[],"customfield_10010":null,"comment":{"startAt":0,"maxResults":0,"total":0,"comments":[]},"timeoriginalestimate":null,"customfield_10011":null,"aggregatetimespent":null}},"timestamp":1504424996620}';

if(empty($str)) return false;

$access_token=$_GET['access_token'];
$access_token='865acac088fc8dacf5ca42fefbb56380b38d16d96f5bbcf50fbf6961a31f2016';

$post=json_decode($str,true);

$action=$post['webhookEvent'];
$is_transition=isset($post['transition']);

include_once(ROOT.'/jira/message.php');
include_once(ROOT.'/dingtalk/notify.php');

$jira_message=new JiraMessage($post);
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

        break;
    }
}

if($is_transition){

    if($jira_message->IsIssueResolved()){

        $result=$dingtalk_notify->issueResolved($jira_message);
    }else if($jira_message->IsIssueReopened()){

        $result=$dingtalk_notify->issueReopened($jira_message);
    }
}
