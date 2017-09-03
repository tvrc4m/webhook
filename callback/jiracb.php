<?php

$post=$_POST;

// 标题
$title=$post['issue']['fields']['summary'];
// issue链接
$url=$post['issue']['self'];
// 优先级
$priority=$post['issue']['fields']['priority'];

@file_put_contents('/tmp/jira.log', var_export($post,true),FILE_APPEND);