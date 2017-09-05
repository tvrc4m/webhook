<?php


$post=$_POST;
$request=$_REQUEST;

@file_put_contents('/tmp/post.log', var_export($request,true),FILE_APPEND);