<?php

class JiraMessage {

    private $host='http://101.201.234.44:8080';

    private $project;

    private $url;

    private $title;

    private $summary;

    private $assignee;

    private $creator;

    private $priority;

    private $issue_type;

    private $issue;

    private $is_transition=false;

    private $is_resolved=false;

    private $is_reopened=false;

    private $is_comment=false;

    private $comment;
    /**
     * 操作人
     * @var string
     */
    private $operator;

    public function __construct($project,$post){
        // 项目名
        $this->project=$project;
        // 标题
        $this->title=$post['issue']['fields']['summary'];
        // 摘要
        $this->summary=$post['issue']['fields']['description'];
        // assign
        $this->assignee=$post['issue']['fields']['assignee']['displayName'];
        // 创建者
        $this->creator=$post['issue']['fields']['creator']['displayName'];
        // 优先级
        $this->priority=$post['issue']['fields']['priority'];
        // issue号
        $this->issue=$post['issue']['key'];
        // issue 类型
        $this->issue_type=$post['issue']['fields']['issuetype']['name'];
        // 进度通知
        $this->is_transition=isset($post['transition']);

        if($this->is_transition){
            // issue的状态是否已完成
            $this->is_resolved=$post['transition']['transitionName']=='开发完成';
            // issue的状态是否已reopen
            $this->is_reopened=$post['transition']['transitionName']=='重新开发';
        }
        // 更新是否是新增备注
        $this->is_comment=$post['comment'];

        if($this->is_comment){

            $this->comment=['content'=>$post['comment']['body'],'author'=>$post['comment']['author']['displayName']];
        }

        $this->operator=$post['user']['displayName'];
        
    }

    /**
     * get jira all register users,key is the name,value is the dingtalk register phone.
     * @return array
     */
    private function getUsers(){

        return
        [
            'admin'=>'15763951212',
            '魏山'=>'15763951212',
            'dawn'=>'18610402391',
            'lishuo'=>'13911516907',
            'nexiy'=>'18811223158',
            'wuchao'=>'13522216112',
            '王娟娟'=>'13161824260',
            '肖阿勇'=>'18612309283',
            '韩萌萌'=>'18330236860',
            'lijialin'=>'15910442846',
            'haohuili'=>'18501219135'
        ];
    }

    /**
     * 获取项目列表
     * @return array
     */
    public function getProjects(){

        return 
        [
            'php',
            'android',
            'ios',
            'go'
        ];
    }

    /**
     * 获取组成员
     * @return array
     */
    public function getGroups(){

        return 
        [
            'php'=>[
                'test'=>['18330236860','15910442846','18501219135'],
                'dev'=>['15763951212','18610402391','18811223158','13522216112','13161824260','18612309283']
            ]
        ];
    }

    public function getProject(){

        return $this->project;
    }

    public function getIssueTitle(){

        return $this->title;
    }

    public function getIssueSummary(){

        return $this->summary;
    }

    /**
     * 返回指定assignee
     * @return string 
     */
    public function getIssueAssignee(){

        return $this->assignee;
    }

    /**
     * 返回指定assign的手机号(用于钉钉)
     * @return string 
     */
    public function getIssueAssigneePhone(){

        $users=$this->getUsers();

        return $users[strtolower($this->assignee)];
    }

    /**
     * get issue priority name and icon
     * @return array
     */
    public function getIssuePriority(){

        return ['icon'=>$this->priority['iconUrl'],'name'=>$this->priority['name']];
    }

    /**
     * get issue link
     * @return string
     */
    public function getIssueUrl(){

        return sprintf("%s/browse/%s",$this->host,$this->issue);
    }

    public function getIssueType(){

        return $this->issue_type;
    }

    public function getIssueNumber(){

        return $this->issue;
    }

    public function getIssueCreator(){

        return $this->creator;
    }

    /**
     * 获取issue新增备注
     * @return array
     */
    public function getIssueNewComment(){

        return $this->comment;
    }

    /**
     * 获取更改issue实际操作人
     * @return array
     */
    public function getIssueOperator(){

        return $this->operator;
    }

    /**
     * get jira host address
     * @return string
     */
    public function getJiraHost(){

        return $this->host;
    }

    public function isTransition(){

        return $this->is_transition;
    }

    public function isComment(){

        return $this->is_comment;
    }

    /**
     * Check issue has resolved
     * @return boolean
     */
    public function IsIssueResolved(){

        return $this->is_resolved;
    }

    /**
     * Check issue has reopened
     * @return  boolean
     */
    public function IsIssueReopened(){

        return $this->is_reopened;
    }
}