<?php

$project = $_GET['project'];
$src_branch = $_GET['branch'];
$access_token = $_GET['access_token'];

if (empty($project) || empty($src_branch)) {
	exit('缺少参数');
}

if($project!='php'){
	exit('只支持php项目');
}

define('ROOT', __DIR__);

include_once ROOT . '/dingtalk/notify.php';
include_once ROOT . '/gitlab/api.php';

$dingtalk_notify = new DingtalkNotify($access_token);
$gitlab_api = new GitlabApi();

$dest_branch = 'develop';
$title = ' merge ' . $src_branch . ' to ' . $dest_branch;
$operator="魏山";

$branchInfo = $gitlab_api->getBranch($src_branch);

if (empty($branchInfo) || isset($branchInfo['message'])) {

	$dingtalk_notify->notifyText("{$src_branch}分支不存在", "{$issue_title}\n\n{$src_branch}分支不存在");

	exit(0);
}

// 发起请求
$result = $gitlab_api->createMergeRequest($src_branch, $dest_branch, $operator . $title);

if (empty($result)) {

	$dingtalk_notify->notifyText($title, $operator . $title . "合并请求失败");
} elseif ($result['message']) {

	$dingtalk_notify->notifyTextUrl($operator . $title, $result['message'], $result['web_url'], BASEURL . '/git-icon.png');
} else {
	// 接受合并请求
	$response = $gitlab_api->acceptMergeRequest($result['id']);

	if (empty($response)) {

		$dingtalk_notify->notifyText($title, $operator . $title . '合并请求失败');
	} elseif ($response['message']) {

		$dingtalk_notify->notifyTextUrl($operator . $title, $response['message'], $result['web_url'], BASEURL . '/git-icon.png');
	} else {
		// 采用gitlab webhook通知
	}
}