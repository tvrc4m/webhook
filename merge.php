<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 'Off');

$project = $_GET['project'];
$src_branch = $_GET['branch'];
$dest_branch = $_GET['dest'];
$key = $_GET['key'];
$access_token = $_GET['access_token'];

if (empty($project) || empty($src_branch)) {
	exit('缺少参数');
}

if($project!='php'){
	exit('只支持php项目');
}

define('ROOT', __DIR__);
define('BASEURL', 'http://webhook.vrcdkj.cn');

if($dest_branch=='develop'){

	$click_merged_file="/var/log/click_develop_merged.log";	
}elseif($dest_branch=='app'){

	$click_merged_file="/var/log/click_app_merged.log";
}

$click_merged_content=@file_get_contents($click_merged_file);

$click_merged_list=array_filter(explode("\n", $click_merged_content));

if(!in_array($key, $click_merged_list)){

	exit('已执行');
}

@unlink($click_merged_file);
// 重建key文件
foreach ($click_merged_list as $list) {
	
	if($list!=$key){

		@file_put_contents($click_merged_file, $list.PHP_EOL,FILE_APPEND);
	}
}

if(in_array($src_branch, ['master','develop','app'])){
	exit('不能合并');
}

include_once ROOT . '/dingtalk/notify.php';
include_once ROOT . '/gitlab/api.php';

if($dest_branch=='develop'){

	$merged_log="/var/log/develop_merged.log";

}elseif($dest_branch=='app'){

	$merged_log="/var/log/app_merged.log";
}

$merged_content=@file_get_contents($merged_log);

$merged_list=array_filter(explode("\n", $merged_content));

$request_merge_id=$request_merge_message=$request_merge_url='';

foreach ($merged_list as $list) {
    
    list($branch,$merge_id,$request_merge_message,$request_merge_url)=array_filter(explode('$$', $list));

    if($src_branch==$branch){

    	$request_merge_id=$merge_id;

    	break;
    }
}

$dingtalk_notify = new DingtalkNotify($access_token);
$gitlab_api = new GitlabApi();

$title = ' merge ' . $src_branch . ' to ' . $dest_branch;
$operator="魏山";

$branchInfo = $gitlab_api->getBranch($src_branch);

if (empty($branchInfo) || isset($branchInfo['message'])) {

	$dingtalk_notify->notifyText("{$src_branch}分支不存在", "{$issue_title}\n\n{$src_branch}分支不存在");

	exit(0);
}

if($request_merge_id){

	// 发起请求
	$result = $gitlab_api->createMergeRequest($src_branch, $dest_branch, $operator . $title);
	@file_put_contents('/tmp/merge_request.log', var_export($result,true),FILE_APPEND);
	if (empty($result)) {

		$dingtalk_notify->notifyText($title, $operator . $title . "合并请求失败");

		exit;
	} elseif ($result['message']) {

		$dingtalk_notify->notifyTextUrl($operator . $title, $result['message'], $result['web_url'], BASEURL . '/git-icon.png');

		exit;
	}

	$request_merge_id=$result['id'];
	$request_merge_message=$result['message'];
	$request_merge_url=$result['web_url'];
}

// 接受合并请求
$response = $gitlab_api->acceptMergeRequest($request_merge_id);

if (empty($response)) {

	$dingtalk_notify->notifyText($title, $operator . $title . '合并请求失败');
} elseif ($response['message']) {

	$dingtalk_notify->notifyTextUrl($operator . $title, $request_merge_message, $request_merge_url, BASEURL . '/git-icon.png');
} else {
	// 采用gitlab webhook通知
}
