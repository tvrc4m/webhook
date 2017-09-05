<?php

$env=$_GET['env'];
$branch=$_GET['branch'];

$env='test';
$branch='master';

if(empty($env) || empty($branch)) exit('缺少参数');

define('ROOT', __DIR__);

include_once(ROOT.'/jenkins/cli.php');

$cli=new JenkinsCli();

$result=$cli->deploy($env,$branch);

print_r($result);