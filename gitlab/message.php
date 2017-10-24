<?php

class GitlabMessage {

    private $host='http://gitlab.kanfanews.com';

    private $project;

    private $url;

    private $action;

    private $branch_name;

    private $user_name;
    /**
     * git提交时的用户名
     * @var string
     */
    private $git_user_name;

    private $is_deleted=false;

    private $commits=[];

    public $merge_title;

    public $merge_web_url;

    public $merge_status;

    public function __construct($project,$post){
        // 项目名
        $this->project=$post['project']['name'];
        // 动作
        $this->action=$post['object_kind'];

        $this->branch_name=str_replace('refs/heads/','', $post['ref']);

        $this->user_name=$this->getDisplayName($post['user_name']);
        $this->git_user_name=$post['user_username'];

        $this->commits=[];

        if($post['commits']){

            foreach ($post['commits'] as $commit) {

                if(strpos($commit['message'], '查看合并请求')){

                    $this->commits=[];
                    break;
                }

                if($git_user_name==$commit['author']['name']){

                    $this->commits[]=[
                        'username'=>$this->getDisplayName(trim($commit['author']['name'])),
                        'message'=>$commit['message'],
                        'url'=>$commit['url'],
                        'create_date'=>date("Y-m-d H:i",strtotime($commit['timestamp']))
                    ];
                }
            }
        }else{

            $this->is_deleted=true;
        }

        $this->merge_title=$post['object_attributes']['title'];
        $this->merge_web_url=$post['object_attributes']['url'];
        $this->merge_status=$post['object_attributes']['state'];
        $this->merge_image=BASEURL."/git-icon.png";
    }

    /**
     * 获取过滤分支--不推送通知
     * @return []
     */
    public function getFilterBranch(){

        return ['master'];
    }

    public function getDisplayName($name){

        $users=['wei shan'=>'魏山','tvrc4m'=>'魏山','lishou'=>'李硕','wjj'=>'王娟娟','wangjuanjuan'=>'王娟娟','dawn'=>'郑生齐','xiaoayong'=>'肖阿勇','wuchao'=>'武超'];

        return isset($users[$name])?$users[$name]:$name;
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

    public function isDeleted(){

        return $this->is_deleted;
    }
}