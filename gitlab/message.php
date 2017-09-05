<?php

class GitlabMessage {

    private $host='http://gitlab.kanfanews.com';

    private $project;

    private $url;

    private $action;

    private $branch_name;

    private $user_name;

    private $commits=[];

    public function __construct($project,$post){
        // 项目名
        $this->project=$post['project']['name'];
        // 动作
        $this->action=$post['object_kind'];

        $this->branch_name=str_replace('refs/heads/','', $post['ref']);

        $this->user_name=$post['user_name'];

        $this->commits=[];

        if($post['commits']){

            foreach ($post['commits'] as $commit) {
                
                $this->commits[]=[
                    'username'=>$commit['author']['name'],
                    'message'=>$commit['message'],
                    'url'=>$commit['url'],
                    'create_date'=>$commit['timestamp']
                ];
            }
        }
    }

    /**
     * 获取过滤分支--不推送通知
     * @return []
     */
    public function getFilterBranch(){

        return ['master','develop','app'];
    }

    public function getProject(){

        return $this->project;
    }

    public function getBranchName(){

        return $this->branch_name;
    }

    public function getUserName(){

        return $this->user_name;
    }

    public function getCommits(){

        return $this->commits;
    }
}