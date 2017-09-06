<?php

define('ROOT', dirname(__dir__));

$str = file_get_contents('php://input');

$str='{"object_kind":"push","event_name":"push","before":"cc5941e098d0dfe63bc127e2e7c0273e55161f75","after":"2617501a403b1adeeb668e98cf79aac50016fc58","ref":"refs/heads/test","checkout_sha":"2617501a403b1adeeb668e98cf79aac50016fc58","message":null,"user_id":4,"user_name":"wei shan","user_username":"shan","user_email":"weishan@kanfanews.com","user_avatar":"/uploads/system/user/avatar/4/avatar.png","project_id":3,"project":{"name":"news","description":"","web_url":"http://gitlab.kanfanews.com/php/news","avatar_url":null,"git_ssh_url":"git@gitlab.kanfanews.com:php/news.git","git_http_url":"http://gitlab.kanfanews.com/php/news.git","namespace":"php","visibility_level":0,"path_with_namespace":"php/news","default_branch":"master","homepage":"http://gitlab.kanfanews.com/php/news","url":"git@gitlab.kanfanews.com:php/news.git","ssh_url":"git@gitlab.kanfanews.com:php/news.git","http_url":"http://gitlab.kanfanews.com/php/news.git"},"commits":[{"id":"2617501a403b1adeeb668e98cf79aac50016fc58","message":"test\n","timestamp":"2017-09-06T09:08:09+08:00","url":"http://gitlab.kanfanews.com/php/news/commit/2617501a403b1adeeb668e98cf79aac50016fc58","author":{"name":"tvrc4m","email":"weishan@kanfanews.com"},"added":[],"modified":["index.html"],"removed":[]}],"total_commits_count":1,"repository":{"name":"news","url":"git@gitlab.kanfanews.com:php/news.git","description":"","homepage":"http://gitlab.kanfanews.com/php/news","git_http_url":"http://gitlab.kanfanews.com/php/news.git","git_ssh_url":"git@gitlab.kanfanews.com:php/news.git","visibility_level":0}}';

if(empty($str)) return false;

@file_put_contents('/tmp/gitlab.log', $str.PHP_EOL,FILE_APPEND);

$project=$_GET['project'];

$access_token = $_SERVER['HTTP_X_GITLAB_TOKEN'];
$access_token='72f583366e685e7de36cd25715d77e3cc5bb07a512bb5ebd5e8da9ddb0c32b07';

$post=json_decode($str,true);
print_r($post);
$action=$post['object_kind'];

include_once(ROOT.'/gitlab/message.php');
include_once(ROOT.'/dingtalk/notify.php');

$gitlab_message=new GitlabMessage($project,$post);
$dingtalk_notify=new DingtalkNotify($access_token);
echo $action;
switch ($action) {

    case 'push':
    {
        $result=$dingtalk_notify->gitPush($gitlab_message);
        print_r($result);
        break;   
    }
}
